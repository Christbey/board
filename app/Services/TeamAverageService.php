<?php

namespace App\Services;

use App\Models\NflTeamSchedule;
use Illuminate\Support\Collection;

class TeamAverageService
{
    public function getAveragePointsForMatchup($teamId1, $teamId2, $type, $seasonRange): float
    {
        $oppositeType = $type === 'home' ? 'away' : 'home';

        $query = NflTeamSchedule::where("team_id_{$type}", $teamId1)
            ->where("team_id_{$oppositeType}", $teamId2)
            ->whereNotNull("{$type}_pts")
            ->where('game_status', 'Completed');

        if ($seasonRange) {
            $query->whereBetween('game_date', $seasonRange);
        }

        $totalPoints = $query->sum("{$type}_pts");
        $totalGames = $query->count();

        return $totalGames > 0 ? (float)$totalPoints / $totalGames : 0;
    }

    public function getAveragePoints($teamId, $type, $seasonRange): float
    {
        $query = NflTeamSchedule::where("team_id_{$type}", $teamId)
            ->whereNotNull("{$type}_pts")
            ->where('game_status', 'Completed');

        if ($seasonRange) {
            $query->whereBetween('game_date', $seasonRange);
        }

        $totalPoints = $query->sum("{$type}_pts");
        $totalGames = $query->count();

        return $totalGames > 0 ? (float)$totalPoints / $totalGames : 0;
    }

    public function getTeamIds(): Collection
    {
        return NflTeam::pluck('id');
    }
}
