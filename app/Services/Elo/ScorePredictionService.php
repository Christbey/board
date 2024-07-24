<?php

namespace App\Services\Elo;

class ScorePredictionService
{
    private TeamRatingManager $teamRatingManager;
    private EloCalculator $eloCalculator;

    public function __construct(TeamRatingManager $teamRatingManager, EloCalculator $eloCalculator)
    {
        $this->teamRatingManager = $teamRatingManager;
        $this->eloCalculator = $eloCalculator;
    }

    public function getActualScorePrediction(
        int   $teamA,
        int   $teamB,
        float $distance,
        bool  $neutralSite = false,
        bool  $noFans = false,
        bool  $isPlayoff = false,
        float $homeOdds = 0.0,
        float $awayOdds = 0.0
    ): array
    {
        $eloDiff = $this->teamRatingManager->getRatings()[$teamA] - $this->teamRatingManager->getRatings()[$teamB] + $this->eloCalculator->calculateHomeFieldAdjustment($teamA, $teamB, $distance, $neutralSite, $noFans);
        if ($isPlayoff) {
            $eloDiff *= $this->eloCalculator->playoffMultiplier;
        }
        $oddsAdjustment = ($homeOdds - $awayOdds) * $this->eloCalculator->oddsMultiplier; // Use multiplier from config
        $pointSpread = $this->eloCalculator->calculateExpectedPointSpread($eloDiff + $oddsAdjustment);
        $averagePoints = $this->eloCalculator->averagePoints;

        $expectedScoreA = $averagePoints + ($pointSpread / 2);
        $expectedScoreB = $averagePoints - ($pointSpread / 2);

        return [
            'teamA' => round($expectedScoreA),
            'teamB' => round($expectedScoreB)
        ];
    }
}
