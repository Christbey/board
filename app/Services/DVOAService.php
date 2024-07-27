<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class DVOAService
{
    public function calculateTeamDVOA($teamId)
    {
        // Total rush yards and plays for the team
        $teamTotalRushYards = DB::table('nfl_play_by_play')
            ->where('team_id', $teamId)
            ->sum('rush_yds');

        $teamTotalPlays = DB::table('nfl_play_by_play')
            ->where('team_id', $teamId)
            ->count();

        // League average rush yards per play
        $leagueTotalRushYards = DB::table('nfl_play_by_play')
            ->sum('rush_yds');

        $leagueTotalPlays = DB::table('nfl_play_by_play')
            ->count();

        if ($teamTotalPlays == 0 || $leagueTotalPlays == 0) {
            return 0; // Return 0 if there are no plays to avoid division by zero
        }

        $leagueAverageRushYardsPerPlay = $leagueTotalRushYards / $leagueTotalPlays;

        // Calculate team average rush yards per play
        $teamAverageRushYardsPerPlay = $teamTotalRushYards / $teamTotalPlays;

        // Calculate DVOA
        $dvoa = (($teamAverageRushYardsPerPlay - $leagueAverageRushYardsPerPlay) / $leagueAverageRushYardsPerPlay) * 100;

        return $dvoa;
    }
}
