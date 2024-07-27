<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class EloRatingService
{
    public function getTeamEloRating($teamId)
    {
        return DB::table('elo_ratings')
            ->where('team_id', $teamId)
            ->value('rating');
    }

    public function adjustStatsBasedOnElo($playerAvgStat, $playerTeamElo, $opposingTeamElo)
    {
        $eloDifference = $playerTeamElo - $opposingTeamElo;
        return $playerAvgStat + ($eloDifference / 100 * $playerAvgStat);
    }
}
