<?php

namespace App\Services;

use App\Models\NflTeamSchedule;
use App\Models\NflOdds;
use App\Models\NflPlayerStat;
use DB;
use Exception;
use Phpml\Classification\Linear\LogisticRegression;
use Phpml\Dataset\ArrayDataset;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Phpml\Exception\InvalidArgumentException;

class DataPreparationService
{
    public function fetchData(): array
    {
        $schedules = NflTeamSchedule::all()->toArray();

        if (empty($schedules)) {
            return ['message' => 'No data found in nfl_team_schedules table'];
        }

        $cleanedSchedules = $this->cleanData($schedules);
        [$trainData, $futureGames] = $this->splitData($cleanedSchedules);

        if (empty($trainData)) {
            return ['message' => 'No training data available'];
        }

        $model = $this->trainModel($trainData);
        $predictions = $this->makePredictions($model, $futureGames);

        Log::info('Predictions: ', $predictions);

        if (empty($predictions)) {
            Log::info('Future games data: ', $futureGames);
        }

        $winCountsWithNames = $this->calculateWinCounts($predictions);

        return compact('predictions', 'winCountsWithNames');
    }

    private function cleanData(array $data): array
    {
        return array_map(function ($record) {
            return array_map(function ($value) {
                return is_null($value) ? 0 : $value;
            }, $record);
        }, $data);
    }

    private function normalize($value): float|int
    {
        return $value / 100;
    }

    private function splitData(array $data): array
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

        Log::info('Training data: ', $trainData);
        Log::info('Future games data: ', $futureGames);

        return [$trainData, $futureGames];
    }

    private function trainModel(array $trainData): LogisticRegression
    {
        $samples = array_map(function ($record) {
            return [$this->normalize($record['home_pts']), $this->normalize($record['away_pts'])];
        }, $trainData);

        $targets = array_map(function ($record) {
            return $record['home_result'] === 'W' ? 1 : 0;
        }, $trainData);

        Log::info('Training samples: ', $samples);
        Log::info('Training targets: ', $targets);

        $dataset = new ArrayDataset($samples, $targets);
        $model = new LogisticRegression();
        $model->train($dataset->getSamples(), $dataset->getTargets());

        return $model;
    }

    private function makePredictions($model, $futureGames): array
    {
        $teamAverages = $this->calculateTeamAverages();
        $predictions = [];

        foreach ($futureGames as $record) {
            // Extract team abbreviations from game_id
            preg_match('/(\d{8})_([A-Z]+)@([A-Z]+)/', $record['game_id'], $matches);
            if (count($matches) !== 4) {
                Log::error('Invalid game_id format', ['game_id' => $record['game_id']]);
                continue;
            }

            $homeTeamAbbr = $matches[3];
            $awayTeamAbbr = $matches[2];

            // Fetch team IDs and names based on abbreviations
            $homeTeam = DB::table('nfl_teams')->where('abbreviation', $homeTeamAbbr)->first();
            $awayTeam = DB::table('nfl_teams')->where('abbreviation', $awayTeamAbbr)->first();

            if (!$homeTeam || !$awayTeam) {
                Log::error('Team not found for abbreviations', compact('homeTeamAbbr', 'awayTeamAbbr'));
                continue;
            }

            $homeTeamId = $homeTeam->id;
            $awayTeamId = $awayTeam->id;

            $homeAvg = $teamAverages[$homeTeamId] ?? ['home_avg' => 0, 'total_point_over' => 0];
            $awayAvg = $teamAverages[$awayTeamId] ?? ['away_avg' => 0, 'total_point_over' => 0];

            $predictedHomePts = round($homeAvg['home_avg']);
            $predictedAwayPts = round($awayAvg['away_avg']);

            $odds = $this->getOddsForGame($homeTeamId, $awayTeamId, $record['game_date']);
            $homeQBR = $this->calculateQBR($homeTeamId);
            $awayQBR = $this->calculateQBR($awayTeamId);

            if ($odds) {
                list($predictedHomePts, $predictedAwayPts) = $this->adjustPredictionsWithOdds(
                    $predictedHomePts,
                    $predictedAwayPts,
                    $odds,
                    $homeQBR,
                    $awayQBR,
                    $homeAvg,
                    $awayAvg,
                    $homeTeamId,
                    $awayTeamId
                );
            }

            // Determine the predicted winner
            $predictedWinner = $predictedHomePts > $predictedAwayPts ? 'Home' : 'Away';

            $predictions[] = [
                'game_id' => $record['game_id'],
                'home_team_id' => $homeTeamId,
                'home_team_name' => $homeTeam->name,
                'away_team_id' => $awayTeamId,
                'away_team_name' => $awayTeam->name,
                'predicted_winner' => $predictedWinner,
                'home_pts' => $predictedHomePts,
                'away_pts' => $predictedAwayPts,
                'home_qbr' => $homeQBR,
                'away_qbr' => $awayQBR,
            ];
        }

        Log::info('Generated predictions: ', $predictions);

        return $predictions;
    }

    private function calculateTeamAverages(): array
    {
        $config = $this->getConfig();
        $teamAverages = [];
        $teams = DB::table('nfl_teams')->pluck('id');

        foreach ($teams as $teamId) {
            $homeAvg = NflTeamSchedule::where('team_id_home', $teamId)
                ->where('home_pts', '>', 0)
                ->where('home_pts', '<=', $config['homePtsMax']) // Exclude games where home team scored over max points
                ->avg('home_pts') + 2 ?: 0;

            $awayAvg = NflTeamSchedule::where('team_id_away', $teamId)
                ->where('away_pts', '>', 0)
                ->where('away_pts', '<=', $config['awayPtsMax']) // Exclude games where away team scored over max points
                ->avg('away_pts') ?: 0;

            // Join with nfl_odds to fetch total_point_over
            $totalPointOver = NflTeamSchedule::join('nfl_odds', function ($join) {
                $join->on('nfl_team_schedules.team_id_home', '=', 'nfl_odds.home_team_id')
                    ->orOn('nfl_team_schedules.team_id_away', '=', 'nfl_odds.away_team_id');
            })->where(function ($query) use ($teamId) {
                $query->where('nfl_team_schedules.team_id_home', $teamId)
                    ->orWhere('nfl_team_schedules.team_id_away', $teamId);
            })->avg('nfl_odds.total_over_point') ?: 0;

            $teamAverages[$teamId] = [
                'home_avg' => $homeAvg,
                'away_avg' => $awayAvg,
                'total_point_over' => $totalPointOver
            ];
        }

        return $teamAverages;
    }

    private function adjustPredictionsWithOdds($predictedHomePts, $predictedAwayPts, $odds, $homeQBR, $awayQBR, $homeAvg, $awayAvg, $homeTeamId, $awayTeamId): array
    {
        $config = $this->getConfig();

        // Apply under/over logic
        $totalPoints = $homeAvg['total_point_over'] + $awayAvg['total_point_over'];

        // Split total points over between home and away teams
        $homePtsFromTotal = $totalPoints * 0.53;
        $awayPtsFromTotal = $totalPoints * 0.47;

        if (is_object($odds)) {
            $spreadHomePoints = (float)$odds->spread_home_point;
            $spreadAwayPoints = (float)$odds->spread_away_point;

            // Adjust predictions based on spread and total points logic
            if ($spreadHomePoints < 0) {
                // Home team is favorite
                $homePtsFromTotal += $spreadHomePoints + $config['spreadAdjustment'];
                $awayPtsFromTotal += $spreadAwayPoints;
            } else {
                // Home team is underdog
                $homePtsFromTotal += 0;
            }
        } else {
            Log::warning('Odds not found for game', compact('homeTeamId', 'awayTeamId'));
        }

        // Incorporate power rankings
        $homePowerRank = $config['powerRankings'][$homeTeamId] ?? 16; // Default to mid value if not found
        $awayPowerRank = $config['powerRankings'][$awayTeamId] ?? 16; // Default to mid value if not found
        $powerRankDifference = $awayPowerRank - $homePowerRank;

        // Apply power ranking influence
        $homePtsFromTotal += $powerRankDifference * $config['powerRankingInfluence'];
        $awayPtsFromTotal -= $powerRankDifference * $config['powerRankingInfluence'];

        // Apply scaling factor to adjust scores closer to average game score
        $predictedHomePts = $homePtsFromTotal * $config['scalingFactor'] + $config['homeAdjustment'];
        $predictedAwayPts = $awayPtsFromTotal * $config['scalingFactor'];

        return [round($predictedHomePts, 2), round($predictedAwayPts, 2)];
    }

    private function calculateQBR($teamId): float
    {
        // Fetch QB stats for the given team, excluding rows with null values
        $qbrStats = NflPlayerStat::join('nfl_players', 'nfl_player_stats.player_id', '=', 'nfl_players.player_id')
            ->where('nfl_players.pos', 'QB')
            ->where('nfl_player_stats.team_id', $teamId)
            ->whereNotNull('nfl_player_stats.pass_attempts')
            ->whereNotNull('nfl_player_stats.pass_completions')
            ->whereNotNull('nfl_player_stats.pass_yards')
            ->whereNotNull('nfl_player_stats.pass_td')
            ->whereNotNull('nfl_player_stats.pass_int')
            ->orderBy('nfl_player_stats.created_at', 'desc') // Get the most recent stats
            ->first(['pass_attempts', 'pass_completions', 'pass_yards', 'pass_td', 'pass_int']);

        // Log fetched data for debugging
        Log::info('Team ID: ' . $teamId);
        Log::info('Fetched QB stats: ', (array)$qbrStats);

        // If no stats are found, return 0
        if (is_null($qbrStats)) {
            return 0.0;
        }

        // Calculate total stats
        $totalAttempts = $qbrStats->pass_attempts;
        $totalCompletions = $qbrStats->pass_completions;
        $totalYards = $qbrStats->pass_yards;
        $totalTouchdowns = $qbrStats->pass_td;
        $totalInterceptions = $qbrStats->pass_int;

        // Log totals for debugging
        Log::info('Total Attempts: ' . $totalAttempts);
        Log::info('Total Completions: ' . $totalCompletions);
        Log::info('Total Yards: ' . $totalYards);
        Log::info('Total Touchdowns: ' . $totalTouchdowns);
        Log::info('Total Interceptions: ' . $totalInterceptions);

        // If no attempts, return 0
        if ($totalAttempts === 0) {
            return 0.0;
        }

        // Calculate QBR components
        $completionRate = max(0, min(2.375, ($totalCompletions / $totalAttempts - 0.3) * 5));
        $yardsPerAttempt = max(0, min(2.375, (($totalYards / $totalAttempts) - 3) * 0.25));
        $touchdownRate = max(0, min(2.375, ($totalTouchdowns / $totalAttempts) * 20));
        $interceptionRate = max(0, min(2.375, 2.375 - (($totalInterceptions / $totalAttempts) * 25)));

        // Log QBR components for debugging
        Log::info('Completion Rate: ' . $completionRate);
        Log::info('Yards Per Attempt: ' . $yardsPerAttempt);
        Log::info('Touchdown Rate: ' . $touchdownRate);
        Log::info('Interception Rate: ' . $interceptionRate);

        // Calculate and return the final QBR
        $qbr = (($completionRate + $yardsPerAttempt + $touchdownRate + $interceptionRate) / 6) * 100;
        Log::info('Calculated QBR: ' . $qbr);

        return $qbr;
    }

    private function calculateWinCounts(array $predictions): array
    {
        $winCounts = [];

        foreach ($predictions as $prediction) {
            $winningTeamId = $prediction['predicted_winner'] === 'Home' ? $prediction['home_team_id'] : $prediction['away_team_id'];

            if (!isset($winCounts[$winningTeamId])) {
                $winCounts[$winningTeamId] = 0;
            }

            $winCounts[$winningTeamId]++;
        }

        $winCountsWithNames = [];
        foreach ($winCounts as $teamId => $winCount) {
            $teamName = DB::table('nfl_teams')->where('id', $teamId)->value('name');
            $winCountsWithNames[] = ['team_name' => $teamName, 'win_count' => $winCount];
        }

        return $winCountsWithNames;
    }

    private function getOddsForGame($homeTeamId, $awayTeamId, $gameDate): ?NflOdds
    {
        try {
            $gameDateString = Carbon::parse($gameDate)->toDateString();
        } catch (Exception $e) {
            Log::error('Failed to parse game date: ' . $gameDate);
            return null;
        }

        $odds = NflOdds::where('home_team_id', $homeTeamId)
            ->where('away_team_id', $awayTeamId)
            ->whereDate('commence_time', '=', $gameDateString)
            ->first();

        Log::info('Odds Lookup: ', compact('homeTeamId', 'awayTeamId', 'gameDateString', 'odds'));

        return $odds;

    }

    private function getConfig(): array
    {
        return [
            'scalingFactor' => 0.35,
            'homeAdjustment' => 2.5,
            'spreadAdjustment' => 2.5,
            'powerRankingInfluence' => 0.3,
            'homePtsMax' => 28,
            'awayPtsMax' => 24,
            'powerRankings' => [
                1 => 25,  // Arizona Cardinals
                2 => 14,  // Atlanta Falcons
                3 => 4,   // Baltimore Ravens
                4 => 8,   // Buffalo Bills
                5 => 32,  // Carolina Panthers
                6 => 15,  // Chicago Bears
                7 => 5,   // Cincinnati Bengals
                8 => 13,  // Cleveland Browns
                9 => 9,   // Dallas Cowboys
                10 => 31, // Denver Broncos
                11 => 3,  // Detroit Lions
                12 => 12, // Green Bay Packers
                13 => 7,  // Houston Texans
                14 => 18, // Indianapolis Colts
                15 => 19, // Jacksonville Jaguars
                16 => 2,  // Kansas City Chiefs
                17 => 11, // Miami Dolphins
                18 => 6,  // Minnesota Vikings
                19 => 29, // New England Patriots
                20 => 24, // New Orleans Saints
                21 => 30, // New York Giants
                22 => 20, // New York Jets
                23 => 27, // Las Vegas Raiders
                24 => 10,  // Philadelphia Eagles
                25 => 17, // Pittsburgh Steelers
                26 => 21, // Los Angeles Chargers
                27 => 1, // San Francisco 49ers
                28 => 28, // Seattle Seahawks
                29 => 16, // Los Angeles Rams
                30 => 26, // Tampa Bay Buccaneers
                31 => 22, // Tennessee Titans
                32 => 23  // Washington Commanders
            ]
        ];
    }
}
