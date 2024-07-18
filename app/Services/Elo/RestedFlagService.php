<?php

namespace App\Services\Elo;

use App\Models\NflTeamSchedule;
use Carbon\Carbon;

class RestedFlagService
{
    public function isTeamRested($teamId, $gameDate): bool
    {
        $gameDate = Carbon::parse($gameDate);

        // Check for recent games as either home or away team
        $lastGame = NflTeamSchedule::where(function ($query) use ($teamId) {
            $query->where('team_id_home', $teamId)
                ->orWhere('team_id_away', $teamId);
        })
            ->whereDate('game_date', '<', $gameDate)
            ->orderBy('game_date', 'desc')
            ->first();

        if ($lastGame) {
            $lastGameDate = Carbon::parse($lastGame->game_date);
            $daysSinceLastGame = $gameDate->diffInDays($lastGameDate);

            // Check for bye week within the last two weeks
            $twoWeeksAgo = $gameDate->copy()->subDays(14);
            $hasByeWeek = !NflTeamSchedule::where(function ($query) use ($teamId) {
                $query->where('team_id_home', $teamId)
                    ->orWhere('team_id_away', $teamId);
            })
                ->whereBetween('game_date', [$twoWeeksAgo, $gameDate])
                ->exists();

            // Check if the team has played multiple games within the last 10 days
            $recentGames = NflTeamSchedule::where(function ($query) use ($teamId) {
                $query->where('team_id_home', $teamId)
                    ->orWhere('team_id_away', $teamId);
            })
                ->whereBetween('game_date', [$gameDate->copy()->subDays(10), $gameDate])
                ->count();

            // Consider travel distance and home games
            $homeGames = NflTeamSchedule::where('team_id_home', $teamId)
                ->whereBetween('game_date', [$gameDate->copy()->subDays(10), $gameDate])
                ->count();

            // Logic to determine if the team is rested
            if ($daysSinceLastGame > 7 || $hasByeWeek || ($recentGames <= 1 && $homeGames > 0)) {
                return true;
            }
        }

        return false;
    }
}
