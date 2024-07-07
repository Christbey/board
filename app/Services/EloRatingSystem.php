<?php

namespace App\Services;

use App\Models\NflPlayerStat;
use Illuminate\Support\Facades\DB;

class EloRatingSystem
{
    private $ratings;
    private $qbRatings;
    private $homeFieldAdvantage;
    private $kFactor;
    private $distanceFactor;
    private $restBonus;
    private $playoffMultiplier;

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

    private function calculateExpectedScore($ratingA, $ratingB, $isPlayoff = false)
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

    private function calculateMarginOfVictoryMultiplier($winnerScore, $loserScore, $eloDiff)
    {
        $pointDiff = $winnerScore - $loserScore;
        return log($pointDiff + 1) * (2.2 / (($eloDiff * 0.001) + 2.2));
    }

    public function updateRatings($homeTeam, $awayTeam, $homeScore, $awayScore, $distance, $homeRested = false, $awayRested = false, $neutralSite = false, $noFans = false, $isPlayoff = false, $homeQbChange = false, $awayQbChange = false)
    {
        $homeFieldAdjustment = $this->calculateHomeFieldAdjustment($homeTeam, $awayTeam, $distance, $neutralSite, $noFans);
        $homeRating = $this->ratings[$homeTeam] + $homeFieldAdjustment;
        $awayRating = $this->ratings[$awayTeam];

        $homeQBRating = $this->qbRatings[$homeTeam];
        $awayQBRating = $this->qbRatings[$awayTeam];

        if ($homeRested) {
            $homeRating += $this->restBonus;
        }
        if ($awayRested) {
            $awayRating += $this->restBonus;
        }

        if ($homeQbChange) {
            $homeRating += $homeQBRating;
        }
        if ($awayQbChange) {
            $awayRating += $awayQBRating;
        }

        $expectedHomeScore = $this->calculateExpectedScore($homeRating, $awayRating, $isPlayoff);
        $expectedAwayScore = 1 - $expectedHomeScore;

        $actualHomeScore = $homeScore > $awayScore ? 1 : ($homeScore < $awayScore ? 0 : 0.5);
        $actualAwayScore = 1 - $actualHomeScore;

        $winnerScore = $homeScore > $awayScore ? $homeScore : $awayScore;
        $loserScore = $homeScore < $awayScore ? $homeScore : $awayScore;
        $winnerElo = $homeScore > $awayScore ? $homeRating : $awayRating;
        $eloDiff = abs($homeRating - $awayRating);
        $marginOfVictoryMultiplier = $this->calculateMarginOfVictoryMultiplier($winnerScore, $loserScore, $eloDiff);

        $kFactorAdjusted = $this->kFactor * $marginOfVictoryMultiplier;

        $this->ratings[$homeTeam] += $kFactorAdjusted * ($actualHomeScore - $expectedHomeScore);
        $this->ratings[$awayTeam] += $kFactorAdjusted * ($actualAwayScore - $expectedAwayScore);
    }

    public function getRatings()
    {
        return $this->ratings;
    }

    public function setQbRating($team, $rating)
    {
        $this->qbRatings[$team] = $rating;
    }

    public function calculateWinProbability($teamA, $teamB, $distance, $neutralSite = false, $noFans = false, $isPlayoff = false)
    {
        $eloDiff = $this->ratings[$teamA] - $this->ratings[$teamB] + $this->calculateHomeFieldAdjustment($teamA, $teamB, $distance, $neutralSite, $noFans);
        if ($isPlayoff) {
            $eloDiff *= $this->playoffMultiplier;
        }
        return 1 / (1 + pow(10, -$eloDiff / 400));
    }

    public function calculateQbValue($stats)
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

    public function updateQbRating($team, $qbStats, $opponentDefense)
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
