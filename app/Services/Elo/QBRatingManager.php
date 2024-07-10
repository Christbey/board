<?php

namespace App\Services\Elo;

class QBRatingManager
{
    private array $qbRatings;

    public function initializeQbRatings(): void
    {
        $teams = $this->getAllTeams();
        $this->qbRatings = array_fill_keys($teams, 0);
    }

    public function setQbRating($team, $rating): void
    {
        $this->qbRatings[$team] = $rating;
    }

    public function calculateQbValue($stats): float
    {
        return -2.2 * $stats['pass_attempts'] +
            3.7 * $stats['completions'] +
            ($stats['passing_yards'] / 5) +
            11.3 * $stats['passing_tds'] -
            14.1 * $stats['interceptions'] -
            8 * $stats['sacks'] -
            1.1 * $stats['rush_attempts'] +
            0.6 * $stats['rushing_yards'] +
            15.9 * $stats['rushing_tds'];
    }

    public function updateQbRating($team, $qbStats, $opponentDefense): void
    {
        $defenseAdjustment = $opponentDefense - $this->getLeagueAverageValue();
        $gameValue = $this->calculateQbValue($qbStats) - $defenseAdjustment;
        $this->qbRatings[$team] = 0.9 * $this->qbRatings[$team] + 0.1 * $gameValue;
    }

    public function getLeagueAverageValue()
    {
        return DB::table('nfl_player_stats')->avg(DB::raw('
            -2.2 * pass_attempts +
            3.7 * completions +
            (passing_yards / 5) +
            11.3 * passing_tds -
            14.1 * interceptions -
            8 * sacks -
            1.1 * rush_attempts +
            0.6 * rushing_yards +
            15.9 * rushing_tds
        '));
    }
}
