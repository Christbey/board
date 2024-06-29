<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NflTeamSchedule;
use Phpml\Classification\Linear\LogisticRegression;
use Phpml\Dataset\ArrayDataset;
use Carbon\Carbon;

class DataPreparationController extends Controller
{
    public function fetchData()
    {
        // Fetch data from nfl_team_schedules
        $schedules = NflTeamSchedule::all()->toArray();

        // Ensure data is fetched
        if (empty($schedules)) {
            return view('data_preparation', ['message' => 'No data found in nfl_team_schedules table']);
        }

        // Clean and prepare data
        $cleanedSchedules = $this->cleanData($schedules);

        // Split data into training and future game sets
        [$trainData, $futureGames] = $this->splitData($cleanedSchedules);

        // Ensure training data is available
        if (empty($trainData)) {
            return view('data_preparation', ['message' => 'No training data available']);
        }

        // Train the model
        $model = $this->trainModel($trainData);

        // Make predictions for future games
        $predictions = $this->makePredictions($model, $futureGames);

        // Log predictions for debugging
        \Log::info('Predictions: ', $predictions);

        // Check if predictions are empty and log the future games data for debugging
        if (empty($predictions)) {
            \Log::info('Future games data: ', $futureGames);
        }

        // Pass predictions to the view
        return view('data_preparation', compact('predictions'));
    }

    private function cleanData($data)
    {
        foreach ($data as &$record) {
            foreach ($record as $key => $value) {
                if (is_null($value)) {
                    $record[$key] = 0; // Handle missing values
                } else if (is_numeric($value)) {
                    $record[$key] = $value; // Ensure numeric values are kept as-is
                }
            }
        }
        return $data;
    }

    private function normalize($value)
    {
        // Adjust normalization based on realistic range of your data
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

        // Log the training and future games data for debugging
        \Log::info('Training data: ', $trainData);
        \Log::info('Future games data: ', $futureGames);

        return [$trainData, $futureGames];
    }

    private function trainModel($trainData)
    {
        $samples = [];
        $targets = [];

        foreach ($trainData as $record) {
            $samples[] = [
                $this->normalize($record['home_pts']),
                $this->normalize($record['away_pts'])
                // Add other relevant features here
            ];
            $targets[] = $record['home_result'] === 'W' ? 1 : 0; // 1 for win, 0 for loss
        }

        // Log the training samples and targets for debugging
        \Log::info('Training samples: ', $samples);
        \Log::info('Training targets: ', $targets);

        $dataset = new ArrayDataset($samples, $targets);
        $model = new LogisticRegression();
        $model->train($dataset->getSamples(), $dataset->getTargets());

        return $model;
    }

    private function makePredictions($model, $futureGames)
    {
        $predictions = [];

        // Calculate historical averages for home and away points by team
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

        foreach ($futureGames as $record) {
            $homeTeamId = $record['team_id_home'];
            $awayTeamId = $record['team_id_away'];

            $predictedHomePts = round($teamAverages[$homeTeamId]['home_avg']);
            $predictedAwayPts = round($teamAverages[$awayTeamId]['away_avg']);

            // Determine the predicted winner based on the predicted points
            $predictedWinner = $predictedHomePts > $predictedAwayPts ? 'Home' : 'Away';

            $predictions[] = [
                'game_id' => $record['game_id'],
                'predicted_winner' => $predictedWinner,
                'home_pts' => $predictedHomePts,
                'away_pts' => $predictedAwayPts,
            ];
        }

        // Log the predictions for debugging
        \Log::info('Generated predictions: ', $predictions);

        return $predictions;
    }

    private function isSampleValid($sample)
    {
        // Check if the sample has valid numeric values
        foreach ($sample as $value) {
            if (!is_numeric($value)) {
                return false;
            }
        }
        return true;
    }
}
