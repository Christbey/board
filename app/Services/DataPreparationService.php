<?php

namespace App\Services;

use App\Models\NflTeamSchedule;
use App\Models\NflOdds;
use App\Models\NflPlayerStat;
use DB;
use Exception;
use Illuminate\Http\Request;
use Phpml\Classification\Linear\LogisticRegression;
use Phpml\Dataset\ArrayDataset;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Phpml\Exception\InvalidArgumentException;

class DataPreparationService
{
    protected $config;

    public function __construct()
    {
        $this->config = config('nfl');
    }

    public function fetchData(Request $request): array
    {
        $seasonYear = $request->input('season_year', 2023);
        $sosMethod = $request->input('sos_method', 'schedule');
        $teamId = $request->input('team_id');

        $query = NflTeamSchedule::whereYear('game_date', $seasonYear)
            ->where(function ($query) {
                $query->where('game_week', 'not like', '%preseason%')
                    ->whereMonth('game_date', '!=', 8);
            });

        if ($teamId) {
            $query->where(function ($query) use ($teamId) {
                $query->where('team_id_home', $teamId)
                    ->orWhere('team_id_away', $teamId);
            });
        }

        $schedules = $query->get()->toArray();

        if (empty($schedules)) {
            return ['message' => 'No data found in nfl_team_schedules table'];
        }

        $cleanedSchedules = $this->cleanData($schedules);
        [$trainData, $futureGames] = $this->splitData($cleanedSchedules);

        if (empty($trainData) || count($trainData) < 2) {
            return ['message' => 'No training data available'];
        }

        $model = $this->trainModel($trainData);
        $predictions = $this->makePredictions($futureGames);

        Log::info('Predictions: ', $predictions);

        if (empty($predictions)) {
            Log::info('Future games data: ', $futureGames);
        }

        $winCountsWithNames = $this->calculateWinCounts($predictions);
        $strengthOfSchedule = ($sosMethod === 'power_rankings')
            ? $this->calculateStrengthOfScheduleUsingPowerRankings()
            : $this->calculateStrengthOfSchedule($seasonYear);

        $homeTeamsCoverSpreadCount = $this->calculateHomeTeamsCoveringSpread($seasonYear);

        return compact('predictions', 'winCountsWithNames', 'strengthOfSchedule', 'homeTeamsCoverSpreadCount');
    }

    public function getTeamNames(): array
    {
        return DB::table('nfl_teams')->pluck('name', 'id')->toArray();
    }

    private function cleanData(array $data): array
    {
        return array_map(function ($record) {
            return array_map(function ($value) {
                return $value ?? 0;
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

    private function makePredictions($futureGames): array
    {
        $teamAverages = $this->calculateTeamAverages();
        $predictions = [];

        foreach ($futureGames as $record) {
            preg_match('/(\d{8})_([A-Z]+)@([A-Z]+)/', $record['game_id'], $matches);
            if (count($matches) !== 4) {
                Log::error('Invalid game_id format', ['game_id' => $record['game_id']]);
                continue;
            }

            $homeTeamAbbr = $matches[3];
            $awayTeamAbbr = $matches[2];

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
            } else {
                Log::warning('Odds not found for game', compact('homeTeamId', 'awayTeamId'));
                $predictedHomePts = $predictedHomePts ?: 'N/A';
                $predictedAwayPts = $predictedAwayPts ?: 'N/A';
            }

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
        $teams = DB::table('nfl_teams')->pluck('id');
        $teamAverages = [];

        foreach ($teams as $teamId) {
            $homeAvg = $this->getAveragePoints($teamId, 'home', $this->config['homePtsMax']);
            $awayAvg = $this->getAveragePoints($teamId, 'away', $this->config['awayPtsMax']);
            $totalPointOver = $this->getTotalPointOver($teamId);

            $teamAverages[$teamId] = [
                'home_avg' => $homeAvg,
                'away_avg' => $awayAvg,
                'total_point_over' => $totalPointOver
            ];
        }

        return $teamAverages;
    }

    private function getAveragePoints($teamId, $type, $maxPoints): float
    {
        $column = $type . '_pts';
        $teamColumn = 'team_id_' . $type;

        return NflTeamSchedule::where($teamColumn, $teamId)
            ->where($column, '>', 0)
            ->where($column, '<=', $maxPoints)
            ->avg($column) + 2 ?: 0;
    }

    private function getTotalPointOver($teamId): float
    {
        return NflTeamSchedule::join('nfl_odds', function ($join) {
            $join->on('nfl_team_schedules.team_id_home', '=', 'nfl_odds.home_team_id')
                ->orOn('nfl_team_schedules.team_id_away', '=', 'nfl_odds.away_team_id');
        })
            ->where(function ($query) use ($teamId) {
                $query->where('nfl_team_schedules.team_id_home', $teamId)
                    ->orWhere('nfl_team_schedules.team_id_away', $teamId);
            })
            ->avg('nfl_odds.total_over_point') ?: 0;
    }

    private function adjustPredictionsWithOdds(
        $predictedHomePts,
        $predictedAwayPts,
        $odds,
        $homeQBR,
        $awayQBR,
        $homeAvg,
        $awayAvg,
        $homeTeamId,
        $awayTeamId
    ): array
    {
        $totalPoints = $homeAvg['total_point_over'] + $awayAvg['total_point_over'];
        [$homePtsFromTotal, $awayPtsFromTotal] = $this->splitTotalPoints($totalPoints);

        if ($odds) {
            [$homePtsFromTotal, $awayPtsFromTotal] = $this->adjustPointsWithOdds(
                $homePtsFromTotal,
                $awayPtsFromTotal,
                $odds
            );
        } else {
            Log::warning('Odds not found for game', compact('homeTeamId', 'awayTeamId'));
        }

        [$homePtsFromTotal, $awayPtsFromTotal] = $this->applyPowerRankingInfluence(
            $homePtsFromTotal,
            $awayPtsFromTotal,
            $this->getPowerRanks($homeTeamId, $awayTeamId)
        );

        $predictedHomePts = $this->applyScalingFactor(
            $homePtsFromTotal,
            $this->config['scalingFactor'],
            $this->config['homeAdjustment']
        );
        $predictedAwayPts = $this->applyScalingFactor($awayPtsFromTotal, $this->config['scalingFactor']);

        return [round($predictedHomePts, 2), round($predictedAwayPts, 2)];
    }

    private function splitTotalPoints($totalPoints): array
    {
        return [$totalPoints * 0.53, $totalPoints * 0.47];
    }

    private function adjustPointsWithOdds($homePtsFromTotal, $awayPtsFromTotal, $odds): array
    {
        if ($odds->spread_home_point < 0) {
            $homePtsFromTotal += $odds->spread_home_point + $this->config['spreadAdjustment'];
            $awayPtsFromTotal += $odds->spread_away_point;
        }

        return [$homePtsFromTotal, $awayPtsFromTotal];
    }

    private function getPowerRanks($homeTeamId, $awayTeamId): array
    {
        $homePowerRank = $this->config['powerRankings'][$homeTeamId] ?? 16;
        $awayPowerRank = $this->config['powerRankings'][$awayTeamId] ?? 16;

        return [$homePowerRank, $awayPowerRank];
    }

    private function applyPowerRankingInfluence($homePtsFromTotal, $awayPtsFromTotal, $powerRanks): array
    {
        $powerRankDifference = $powerRanks[1] - $powerRanks[0];
        $homePtsFromTotal += $powerRankDifference * $this->config['powerRankingInfluence'];
        $awayPtsFromTotal -= $powerRankDifference * $this->config['powerRankingInfluence'];

        return [$homePtsFromTotal, $awayPtsFromTotal];
    }

    private function applyScalingFactor($points, $scalingFactor, $adjustment = 0): float
    {
        return $points * $scalingFactor + $adjustment;
    }

    private function calculateQBR($teamId): float
    {
        $qbrStats = NflPlayerStat::join('nfl_players', 'nfl_player_stats.player_id', '=', 'nfl_players.player_id')
            ->where('nfl_players.pos', 'QB')
            ->where('nfl_player_stats.team_id', $teamId)
            ->whereNotNull('nfl_player_stats.pass_attempts')
            ->whereNotNull('nfl_player_stats.pass_completions')
            ->whereNotNull('nfl_player_stats.pass_yards')
            ->whereNotNull('nfl_player_stats.pass_td')
            ->whereNotNull('nfl_player_stats.pass_int')
            ->orderBy('nfl_player_stats.created_at', 'desc')
            ->first(['pass_attempts', 'pass_completions', 'pass_yards', 'pass_td', 'pass_int']);

        Log::info('Team ID: ' . $teamId);
        Log::info('Fetched QB stats: ', (array)$qbrStats);

        if (is_null($qbrStats)) {
            return 0.0;
        }

        $totalAttempts = $qbrStats->pass_attempts;
        $totalCompletions = $qbrStats->pass_completions;
        $totalYards = $qbrStats->pass_yards;
        $totalTouchdowns = $qbrStats->pass_td;
        $totalInterceptions = $qbrStats->pass_int;

        Log::info('Total Attempts: ' . $totalAttempts);
        Log::info('Total Completions: ' . $totalCompletions);
        Log::info('Total Yards: ' . $totalYards);
        Log::info('Total Touchdowns: ' . $totalTouchdowns);
        Log::info('Total Interceptions: ' . $totalInterceptions);

        if ($totalAttempts === 0) {
            return 0.0;
        }

        $completionRate = max(0, min(2.375, ($totalCompletions / $totalAttempts - 0.3) * 5));
        $yardsPerAttempt = max(0, min(2.375, (($totalYards / $totalAttempts) - 3) * 0.25));
        $touchdownRate = max(0, min(2.375, ($totalTouchdowns / $totalAttempts) * 20));
        $interceptionRate = max(0, min(2.375, 2.375 - (($totalInterceptions / $totalAttempts) * 25)));

        Log::info('Completion Rate: ' . $completionRate);
        Log::info('Yards Per Attempt: ' . $yardsPerAttempt);
        Log::info('Touchdown Rate: ' . $touchdownRate);
        Log::info('Interception Rate: ' . $interceptionRate);

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

        return $this->attachTeamNames($winCounts);
    }

    private function attachTeamNames(array $winCounts): array
    {
        $winCountsWithNames = [];

        foreach ($winCounts as $teamId => $winCount) {
            $teamName = $this->getTeamNameById($teamId);
            $winCountsWithNames[] = ['team_name' => $teamName, 'win_count' => $winCount];
        }

        return $winCountsWithNames;
    }

    private function getTeamNameById(int $teamId): string
    {
        return DB::table('nfl_teams')->where('id', $teamId)->value('name');
    }

    private function getOddsForGame($homeTeamId, $awayTeamId, $gameDate): ?NflOdds
    {
        $gameDateString = $this->parseGameDate($gameDate);

        if (is_null($gameDateString)) {
            return null;
        }

        $odds = $this->fetchOdds($homeTeamId, $awayTeamId, $gameDateString);

        $this->logOddsLookup($homeTeamId, $awayTeamId, $gameDateString, $odds);

        return $odds;
    }

    private function parseGameDate($gameDate): ?string
    {
        try {
            return Carbon::parse($gameDate)->toDateString();
        } catch (Exception $e) {
            Log::error('Failed to parse game date: ' . $gameDate);
            return null;
        }
    }

    private function fetchOdds($homeTeamId, $awayTeamId, $gameDateString): ?NflOdds
    {
        return NflOdds::where('home_team_id', $homeTeamId)
            ->where('away_team_id', $awayTeamId)
            ->whereDate('commence_time', '=', $gameDateString)
            ->first();
    }

    private function logOddsLookup($homeTeamId, $awayTeamId, $gameDateString, $odds): void
    {
        Log::info('Odds Lookup: ', compact('homeTeamId', 'awayTeamId', 'gameDateString', 'odds'));
    }

    private function calculateStrengthOfSchedule($seasonYear = 2024): array
    {
        $strengthOfSchedule = [];
        $teams = DB::table('nfl_teams')->pluck('id');

        foreach ($teams as $teamId) {
            $games = $this->getGamesForTeam($teamId, $seasonYear);
            $totalOpponentWins = $this->calculateTotalOpponentWins($games, $seasonYear);
            $totalGames = $games->count();

            $strengthOfSchedule[$teamId] = $this->calculateAverageOpponentWins($totalOpponentWins, $totalGames);
        }

        return $strengthOfSchedule;
    }

    private function getGamesForTeam($teamId, $seasonYear)
    {
        return NflTeamSchedule::where(function ($query) use ($teamId) {
            $query->where('team_id_home', $teamId)
                ->orWhere('team_id_away', $teamId);
        })->whereYear('game_date', $seasonYear)->get();
    }

    private function calculateTotalOpponentWins($games, $seasonYear): int
    {
        $totalOpponentWins = 0;

        foreach ($games as $game) {
            $opponentId = $this->getOpponentId($game, $game->team_id_home);
            $opponentWins = $this->getOpponentWins($opponentId, $seasonYear);
            $totalOpponentWins += $opponentWins;
        }

        return $totalOpponentWins;
    }

    private function getOpponentId($game, $teamId)
    {
        return ($game->team_id_home == $teamId) ? $game->team_id_away : $game->team_id_home;
    }

    private function getOpponentWins($opponentId, $seasonYear): int
    {
        return NflTeamSchedule::where(function ($query) use ($opponentId) {
            $query->where('team_id_home', $opponentId)
                ->where('home_result', 'W')
                ->orWhere('team_id_away', $opponentId)
                ->where('away_result', 'W');
        })->whereYear('game_date', $seasonYear)->count();
    }

    private function calculateAverageOpponentWins($totalOpponentWins, $totalGames): float
    {
        return ($totalGames > 0) ? $totalOpponentWins / $totalGames : 0;
    }

    private function calculateStrengthOfScheduleUsingPowerRankings(): array
    {
        $powerRankings = $this->config['powerRankings'];
        $strengthOfSchedule = [];
        $teams = DB::table('nfl_teams')->pluck('id');

        foreach ($teams as $teamId) {
            $games = $this->fetchTeamGames($teamId);
            $totalPowerRanking = $this->calculateTotalPowerRanking($games, $teamId, $powerRankings);
            $totalGames = $games->count();

            $strengthOfSchedule[$teamId] = $this->calculateAveragePowerRanking($totalPowerRanking, $totalGames);
        }

        return $strengthOfSchedule;
    }

    private function fetchTeamGames($teamId)
    {
        return NflTeamSchedule::where('team_id_home', $teamId)
            ->orWhere('team_id_away', $teamId)
            ->get();
    }

    private function calculateTotalPowerRanking($games, $teamId, $powerRankings): int
    {
        $totalPowerRanking = 0;

        foreach ($games as $game) {
            $opponentId = $this->getOpponentId($game, $teamId);
            $totalPowerRanking += $powerRankings[$opponentId] ?? 16; // Default to mid value if not found
        }

        return $totalPowerRanking;
    }

    private function calculateAveragePowerRanking($totalPowerRanking, $totalGames): float
    {
        return ($totalGames > 0) ? $totalPowerRanking / $totalGames : 0;
    }

    private function calculateHomeTeamsCoveringSpread(int $seasonYear): int
    {
        $homeTeamsCovering = 0;

        $games = NflTeamSchedule::whereYear('game_date', $seasonYear)
            ->where(function ($query) {
                $query->where('game_week', 'not like', '%preseason%')
                    ->whereMonth('game_date', '!=', 8);
            })
            ->get();

        foreach ($games as $game) {
            $odds = NflOdds::where('home_team_id', $game->team_id_home)
                ->where('away_team_id', $game->team_id_away)
                ->whereDate('commence_time', $game->game_date)
                ->first();

            if ($odds) {
                $homeTeamScore = $game->home_pts;
                $awayTeamScore = $game->away_pts;

                if (!is_null($homeTeamScore) && !is_null($awayTeamScore)) {
                    $homeCovering = ($homeTeamScore + $odds->spread_home_point) > $awayTeamScore;
                    if ($homeCovering) {
                        $homeTeamsCovering++;
                    }
                }
            } else {
                Log::warning('Odds not found for game', [
                    'home_team_id' => $game->team_id_home,
                    'away_team_id' => $game->team_id_away,
                    'game_date' => $game->game_date,
                    'game_time' => $game->game_time,
                ]);
            }
        }

        return $homeTeamsCovering;
    }
}
