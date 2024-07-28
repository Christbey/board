<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\NflPlayByPlay;

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

    public function calculateAverageStats($playerId, $period): array
    {
        $query = NflPlayByPlay::where('player_id', $playerId);

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
