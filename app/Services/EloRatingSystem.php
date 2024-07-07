<?php

namespace App\Services;

class EloRatingSystem
{
    private array $ratings;
    private array $qbRatings;
    private mixed $homeFieldAdvantage;
    private mixed $kFactor;
    private mixed $distanceFactor;
    private mixed $restBonus;
    private mixed $playoffMultiplier;

    public function __construct($teams, $initialRating = 1500, $homeFieldAdvantage = 48, $kFactor = 20, $distanceFactor = 4, $restBonus = 25, $playoffMultiplier = 1.2)
    {
        $this->ratings = array_fill_keys($teams, $initialRating);
        $this->qbRatings = array_fill_keys($teams, 0);
        $this->homeFieldAdvantage = $homeFieldAdvantage;
        $this->kFactor = $kFactor;
        $this->distanceFactor = $distanceFactor;
        $this->restBonus = $restBonus;
        $this->playoffMultiplier = $playoffMultiplier;
    }

    private function calculateExpectedScore($ratingA, $ratingB, $isPlayoff = false): float|int
    {
        $eloDiff = $ratingA - $ratingB;
        if ($isPlayoff) {
            $eloDiff *= $this->playoffMultiplier;
        }
        return 1 / (1 + pow(10, -$eloDiff / 400));
    }

    private function calculateHomeFieldAdjustment($homeTeam, $awayTeam, $distance, $neutralSite = false, $noFans = false)
    {
        if ($neutralSite) {
            return $this->distanceFactor * ($distance / 1000);
        }

        $baseAdvantage = $noFans ? 33 : $this->homeFieldAdvantage;
        return $baseAdvantage + $this->distanceFactor * ($distance / 1000);
    }

    public function updateRatings($homeTeam, $awayTeam, $homeScore, $awayScore, $distance, $homeRested = false, $awayRested = false, $neutralSite = false, $noFans = false, $isPlayoff = false, $homeQbChange = false, $awayQbChange = false): void
    {
        $homeFieldAdjustment = $this->calculateHomeFieldAdjustment($homeTeam, $awayTeam, $distance, $neutralSite, $noFans);
        $homeRating = $this->ratings[$homeTeam] + $homeFieldAdjustment + $this->qbRatings[$homeTeam];
        $awayRating = $this->ratings[$awayTeam] + $this->qbRatings[$awayTeam];

        if ($homeRested) {
            $homeRating += $this->restBonus;
        }
        if ($awayRested) {
            $awayRating += $this->restBonus;
        }

        if ($homeQbChange) {
            $homeRating += $this->qbRatings[$homeTeam];
        }
        if ($awayQbChange) {
            $awayRating += $this->qbRatings[$awayTeam];
        }

        $expectedHomeScore = $this->calculateExpectedScore($homeRating, $awayRating, $isPlayoff);
        $expectedAwayScore = 1 - $expectedHomeScore;

        $actualHomeScore = $homeScore > $awayScore ? 1 : ($homeScore < $awayScore ? 0 : 0.5);
        $actualAwayScore = 1 - $actualHomeScore;

        $marginOfVictoryMultiplier = log(abs($homeScore - $awayScore) + 1) * (2.2 / ((($homeRating - $awayRating) * 0.001) + 2.2));
        $kFactorAdjusted = $this->kFactor * $marginOfVictoryMultiplier;

        $this->ratings[$homeTeam] += $kFactorAdjusted * ($actualHomeScore - $expectedHomeScore);
        $this->ratings[$awayTeam] += $kFactorAdjusted * ($actualAwayScore - $expectedAwayScore);
    }

    public function getRatings()
    {
        return $this->ratings;
    }

    public function setQbRating($team, $rating): void
    {
        $this->qbRatings[$team] = $rating;
    }

    public function calculateWinProbability($teamA, $teamB, $distance, $neutralSite = false, $noFans = false, $isPlayoff = false): float|int
    {
        $eloDiff = $this->ratings[$teamA] - $this->ratings[$teamB] + $this->calculateHomeFieldAdjustment($teamA, $teamB, $distance, $neutralSite, $noFans);
        if ($isPlayoff) {
            $eloDiff *= $this->playoffMultiplier;
        }
        return 1 / (1 + pow(10, -$eloDiff / 400));
    }
}
