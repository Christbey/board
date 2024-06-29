<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NflTeamSchedule;
use App\Models\NflOdds;
use Phpml\Classification\Linear\LogisticRegression;
use Phpml\Dataset\ArrayDataset;
use Carbon\Carbon;

class DataPreparationController extends Controller
{
    public function fetchData()
    {
        $schedules = NflTeamSchedule::all()->toArray();

        if (empty($schedules)) {
            return view('data_preparation', ['message' => 'No data found in nfl_team_schedules table']);
        }

        $cleanedSchedules = $this->cleanData($schedules);
        [$trainData, $futureGames] = $this->splitData($cleanedSchedules);

        if (empty($trainData)) {
            return view('data_preparation', ['message' => 'No training data available']);
        }

        $model = $this->trainModel($trainData);
        $predictions = $this->makePredictions($model, $futureGames);

        \Log::info('Predictions: ', $predictions);

        if (empty($predictions)) {
            \Log::info('Future games data: ', $futureGames);
        }

        return view('data_preparation', compact('predictions'));
    }

    private function cleanData($data)
    {
        return array_map(function($record) {
            return array_map(function($value) {
                return is_null($value) ? 0 : (is_numeric($value) ? $value : $value);
            }, $record);
        }, $data);
    }

    private function normalize($value)
    {
        return $value / 100;
    }

    private function splitData($data)
    {
        $trainData = [];
        $futureGames = [];
        $cutoffDate = Carbon::create(2024, 3, 1);

        foreach ($data as $record) {
            $gameDate = Carbon::parse($record['game_date']);
            if ($gameDate->lt($cutoffDate)) {
                $trainData[] = $record;
            } else {
                $futureGames[] = $record;
            }
        }

        \Log::info('Training data: ', $trainData);
        \Log::info('Future games data: ', $futureGames);

        return [$trainData, $futureGames];
    }

    private function trainModel($trainData)
    {
        $samples = array_map(function($record) {
            return [$this->normalize($record['home_pts']), $this->normalize($record['away_pts'])];
        }, $trainData);

        $targets = array_map(function($record) {
            return $record['home_result'] === 'W' ? 1 : 0;
        }, $trainData);

        \Log::info('Training samples: ', $samples);
        \Log::info('Training targets: ', $targets);

        $dataset = new ArrayDataset($samples, $targets);
        $model = new LogisticRegression();
        $model->train($dataset->getSamples(), $dataset->getTargets());

        return $model;
    }

    private function makePredictions($model, $futureGames)
    {
        $teamAverages = $this->calculateTeamAverages();
        $predictions = [];

        foreach ($futureGames as $record) {
            $homeTeamId = $record['team_id_home'];
            $awayTeamId = $record['team_id_away'];

            $predictedHomePts = round($teamAverages[$homeTeamId]['home_avg']);
            $predictedAwayPts = round($teamAverages[$awayTeamId]['away_avg']);

            $odds = $this->getOddsForGame($homeTeamId, $awayTeamId, $record['game_date']);

            if ($odds) {
                list($predictedHomePts, $predictedAwayPts) = $this->adjustPredictionsWithOdds($predictedHomePts, $predictedAwayPts, $odds);
            }

            $predictedWinner = $predictedHomePts > $predictedAwayPts ? 'Home' : 'Away';

            $predictions[] = [
                'game_id' => $record['game_id'],
                'predicted_winner' => $predictedWinner,
                'home_pts' => $predictedHomePts,
                'away_pts' => $predictedAwayPts,
            ];
        }

        \Log::info('Generated predictions: ', $predictions);

        return $predictions;
    }

    private function calculateTeamAverages()
    {
        $teamAverages = [];
        $teams = \DB::table('nfl_teams')->pluck('id');

        foreach ($teams as $teamId) {
            $homeAvg = NflTeamSchedule::where('team_id_home', $teamId)->where('home_pts', '>', 0)->avg('home_pts') ?: 0;
            $awayAvg = NflTeamSchedule::where('team_id_away', $teamId)->where('away_pts', '>', 0)->avg('away_pts') ?: 0;
            $teamAverages[$teamId] = [
                'home_avg' => $homeAvg,
                'away_avg' => $awayAvg,
            ];
        }

        return $teamAverages;
    }

    private function getOddsForGame($homeTeamId, $awayTeamId, $gameDate)
    {
        try {
            $gameDateString = Carbon::parse($gameDate)->toDateString();
        } catch (\Exception $e) {
            \Log::error('Failed to parse game date: ' . $gameDate);
            return null;
        }

        $odds = NflOdds::where('home_team_id', $homeTeamId)
            ->where('away_team_id', $awayTeamId)
            ->whereDate('commence_time', '=', $gameDateString)
            ->first();

        \Log::info('Odds Lookup: ', [
            'home_team_id' => $homeTeamId,
            'away_team_id' => $awayTeamId,
            'game_date' => $gameDateString,
            'odds' => $odds
        ]);

        return $odds;
    }

    private function adjustPredictionsWithOdds($predictedHomePts, $predictedAwayPts, $odds)
    {
        $homeOdds = (float) $odds->h2h_home_price;
        $awayOdds = (float) $odds->h2h_away_price;
        $spreadHomePoints = (float) $odds->spread_home_point;
        $spreadAwayPoints = (float) $odds->spread_away_point;
        $totalOverPoints = (float) $odds->total_over_point;
        $totalUnderPoints = (float) $odds->total_under_point;

        $predictedHomePts += ($homeOdds / 100) + ($spreadHomePoints / 10) + ($totalOverPoints / 10);
        $predictedAwayPts += ($awayOdds / 100) + ($spreadAwayPoints / 10) + ($totalUnderPoints / 10);

        return [$predictedHomePts, $predictedAwayPts];
    }

    public function matchSchedulesWithOdds()
    {
        $schedules = NflTeamSchedule::all();
        \Log::info('Number of schedules fetched: ' . count($schedules));
        $matchedData = [];

        foreach ($schedules as $schedule) {
            if ($schedule->game_time == 'TBD') {
                \Log::info('Skipping schedule with TBD time: ' . $schedule->game_id);
                continue;
            }

            $gameDate = $this->parseDate($schedule->game_date);
            if (!$gameDate) continue;

            $odds = $this->getOddsForGame($schedule->team_id_home, $schedule->team_id_away, $gameDate);
            if ($odds) {
                $matchedData[] = [
                    'schedule' => $schedule,
                    'odds' => $odds
                ];
            }
        }

        \Log::info('Matched Data Count: ', ['count' => count($matchedData)]);
        \Log::info('Matched Data: ', $matchedData);

        return view('matched_schedules', compact('matchedData'));
    }

    private function parseDate($date)
    {
        try {
            return Carbon::parse($date)->toDateString();
        } catch (\Exception $e) {
            \Log::error('Failed to parse date: ' . $date);
            return null;
        }
    }
}
