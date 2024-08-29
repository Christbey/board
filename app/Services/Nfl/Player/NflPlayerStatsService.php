<?php

namespace App\Services\Nfl\Player;

use App\Models\NflPlayByPlay;
use Illuminate\Support\Facades\DB;

class NflPlayerStatsService
{
    public function getPlayerTeamId($playerId)
    {
        return DB::table('nfl_player_stats')
            ->where('player_id', $playerId)
            ->value('team_id');
    }

    public function getPlayerPosition($playerId)
    {
        return DB::table('nfl_players')
            ->where('player_id', $playerId)
            ->value('pos');
    }

    public function getPlayerAvgRushYards($playerId)
    {
        return DB::table('nfl_player_stats')
            ->where('player_id', $playerId)
            ->avg('rush_yards');
    }

    public function getTeamAvgAllowedRushYards($teamId)
    {
        return DB::table('nfl_player_stats')
            ->join('nfl_team_schedules', function ($join) use ($teamId) {
                $join->on('nfl_player_stats.team_id', '=', 'nfl_team_schedules.team_id_home')
                    ->orOn('nfl_player_stats.team_id', '=', 'nfl_team_schedules.team_id_away');
            })
            ->where(function ($query) use ($teamId) {
                $query->where('team_id_home', $teamId)
                    ->orWhere('team_id_away', $teamId);
            })
            ->avg('rush_yards');
    }

    public function getPlayerAvgPassYards($playerId)
    {
        return DB::table('nfl_player_stats')
            ->where('player_id', $playerId)
            ->avg('pass_yards');
    }

    public function getTeamAvgAllowedPassYards($teamId)
    {
        return DB::table('nfl_player_stats')
            ->join('nfl_team_schedules', function ($join) use ($teamId) {
                $join->on('nfl_player_stats.team_id', '=', 'nfl_team_schedules.team_id_home')
                    ->orOn('nfl_player_stats.team_id', '=', 'nfl_team_schedules.team_id_away');
            })
            ->where(function ($query) use ($teamId) {
                $query->where('team_id_home', $teamId)
                    ->orWhere('team_id_away', $teamId);
            })
            ->avg('pass_yards');
    }

    public function getPlayerAvgRecYards($playerId)
    {
        return DB::table('nfl_player_stats')
            ->where('player_id', $playerId)
            ->avg('rec_yards');
    }

    public function getTeamAvgAllowedRecYards($teamId)
    {
        return DB::table('nfl_player_stats')
            ->join('nfl_team_schedules', function ($join) use ($teamId) {
                $join->on('nfl_player_stats.team_id', '=', 'nfl_team_schedules.team_id_home')
                    ->orOn('nfl_player_stats.team_id', '=', 'nfl_team_schedules.team_id_away');
            })
            ->where(function ($query) use ($teamId) {
                $query->where('team_id_home', $teamId)
                    ->orWhere('team_id_away', $teamId);
            })
            ->avg('rec_yards');
    }

    public function calculateAverageStats($playerId, $period = 'FullGame', $year = null): array
    {
        $query = NflPlayByPlay::where('player_id', $playerId);

        // Apply the year filter based on the provided year and game_id
        if ($year) {
            $query->whereIn('game_id', function ($subQuery) use ($year) {
                $subQuery->select('game_id')
                    ->from('nfl_team_schedules') // Replace with the table that stores game_ids and dates if different
                    ->whereYear('game_date', $year); // Assuming game_date is the column with the date
            });
        }

        // Filter by play period
        if ($period !== 'FullGame') {
            switch ($period) {
                case 'Q1&Q2':
                    $query->whereIn('play_period', ['Q1', 'Q2']);
                    break;
                case 'Q3&Q4':
                    $query->whereIn('play_period', ['Q3', 'Q4']);
                    break;
                default:
                    $query->where('play_period', $period);
            }
        }

        // Aggregating stats for the full game or selected periods
        $totalStats = $query->select(
            DB::raw('SUM(kick_yards) as kick_yards'),
            DB::raw('SUM(receptions) as receptions'),
            DB::raw('SUM(targets) as targets'),
            DB::raw('SUM(rec_yds) as rec_yds'),
            DB::raw('SUM(pass_attempts) as pass_attempts'),
            DB::raw('SUM(pass_yds) as pass_yds'),
            DB::raw('SUM(pass_completions) as pass_completions'),
            DB::raw('SUM(rush_yds) as rush_yds'),
            DB::raw('SUM(carries) as carries'),
            DB::raw('COUNT(DISTINCT game_id) as game_count')
        )->first();

        $gameCount = max($totalStats->game_count, 1); // Ensure we don't divide by zero
        $stats = [
            'kick_yards' => $totalStats->kick_yards / $gameCount,
            'receptions' => $totalStats->receptions / $gameCount,
            'targets' => $totalStats->targets / $gameCount,
            'rec_yds' => $totalStats->rec_yds / $gameCount,
            'pass_attempts' => $totalStats->pass_attempts / $gameCount,
            'pass_yds' => $totalStats->pass_yds / $gameCount,
            'pass_completions' => $totalStats->pass_completions / $gameCount,
            'rush_yds' => $totalStats->rush_yds / $gameCount,
            'carries' => $totalStats->carries / $gameCount,
        ];

        return $stats;
    }
}
