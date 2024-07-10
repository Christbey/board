<?php

namespace App\Services\Elo;

class EloCalculator
{
    public mixed $homeFieldAdvantage;
    public mixed $kFactor;
    public mixed $distanceFactor;
    public mixed $restBonus;
    public mixed $playoffMultiplier;
    public mixed $averagePoints;

    public function __construct($homeFieldAdvantage = 48, $kFactor = 20, $distanceFactor = 4, $restBonus = 25, $playoffMultiplier = 1.2, $averagePoints = 21)
    {
        $this->homeFieldAdvantage = $homeFieldAdvantage;
        $this->kFactor = $kFactor;
        $this->distanceFactor = $distanceFactor;
        $this->restBonus = $restBonus;
        $this->playoffMultiplier = $playoffMultiplier;
        $this->averagePoints = $averagePoints;
    }

    public function calculateExpectedScore($ratingA, $ratingB, $isPlayoff = false): float|int
    {
        $eloDiff = $ratingA - $ratingB;
        if ($isPlayoff) {
            $eloDiff *= $this->playoffMultiplier;
        }
        return 1 / (1 + pow(10, -$eloDiff / 400));
    }

    public function calculateMarginOfVictoryMultiplier($winnerScore, $loserScore, $eloDiff): float
    {
        $pointDiff = $winnerScore - $loserScore;
        return log($pointDiff + 1) * (2.2 / (($eloDiff * 0.001) + 2.2));
    }

    public function calculateExpectedPointSpread($eloDiff): float|int
    {
        return $eloDiff / 25; // A typical Elo point spread conversion factor
    }

    public function calculateHomeFieldAdjustment($homeTeam, $awayTeam, $distance, $neutralSite = false, $noFans = false): float|int
    {
        if ($neutralSite) {
            return $this->distanceFactor * ($distance / 1000);
        }

        $baseAdvantage = $noFans ? 33 : $this->homeFieldAdvantage;
        return $baseAdvantage + $this->distanceFactor * ($distance / 1000);
    }
}
