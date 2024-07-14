<?php

namespace App\Services\Elo;

use Illuminate\Support\Facades\DB;

class TeamRatingManager
{
    private array $ratings;
    private EloCalculator $eloCalculator;

    public function __construct()
    {
        $this->eloCalculator = new EloCalculator();
    }

    public function initializeRatings($initialRating = 1500): void
    {
        $teams = $this->getAllTeams();
        $this->ratings = array_fill_keys($teams, $initialRating);
    }

    public function getAllTeams(): array
    {
        return DB::table('nfl_teams')->pluck('id')->toArray();
    }

    public function getRatings(): array
    {
        return $this->ratings;
    }

    public function updateRatings($homeTeam, $awayTeam, $homeScore, $awayScore, $distance, $homeRested = false, $awayRested = false, $neutralSite = false, $noFans = false, $isPlayoff = false, $homeQbChange = false, $awayQbChange = false)
    {
        $homeFieldAdjustment = $this->eloCalculator->calculateHomeFieldAdjustment($homeTeam, $awayTeam, $distance, $neutralSite, $noFans);
        $homeRating = $this->ratings[$homeTeam] + $homeFieldAdjustment;
        $awayRating = $this->ratings[$awayTeam];

        if ($homeRested) {
            $homeRating += $this->eloCalculator->restBonus;
        }
        if ($awayRested) {
            $awayRating += $this->eloCalculator->restBonus;
        }

        $expectedHomeScore = $this->eloCalculator->calculateExpectedScore($homeRating, $awayRating, $isPlayoff);
        $expectedAwayScore = 1 - $expectedHomeScore;

        // Use actual scores if available, otherwise use expected scores
        $actualHomeScore = $homeScore !== null ? ($homeScore > $awayScore ? 1 : ($homeScore < $awayScore ? 0 : 0.5)) : $expectedHomeScore;
        $actualAwayScore = $homeScore !== null ? 1 - $actualHomeScore : $expectedAwayScore;

        $winnerScore = max($homeScore, $awayScore);
        $loserScore = min($homeScore, $awayScore);
        $eloDiff = abs($homeRating - $awayRating);
        $marginOfVictoryMultiplier = $this->eloCalculator->calculateMarginOfVictoryMultiplier($winnerScore, $loserScore, $eloDiff);

        $kFactorAdjusted = $this->eloCalculator->kFactor * $marginOfVictoryMultiplier;

        $this->ratings[$homeTeam] += $kFactorAdjusted * ($actualHomeScore - $expectedHomeScore);
        $this->ratings[$awayTeam] += $kFactorAdjusted * ($actualAwayScore - $expectedAwayScore);
    }
}
