<?php

namespace App\Services\Elo;

use App\Models\NflTeamSchedule;
use Illuminate\Support\Facades\Config;

class EloCalculator
{
    public mixed $homeFieldAdvantage;
    public mixed $kFactor;
    public mixed $distanceFactor;
    public mixed $restBonus;
    public mixed $playoffMultiplier;
    public mixed $averagePoints;
    public mixed $oddsMultiplier;
    public mixed $scalingFactor;
    public mixed $homeAdjustment;
    public mixed $spreadAdjustment;

    public function __construct()
    {
        $eloConfig = Config::get('nfl.elo');

        $this->homeFieldAdvantage = $eloConfig['home_field_advantage'];
        $this->kFactor = $eloConfig['k_factor'];
        $this->distanceFactor = $eloConfig['distance_factor'];
        $this->restBonus = $eloConfig['rest_bonus'];
        $this->playoffMultiplier = $eloConfig['playoff_multiplier'];
        $this->averagePoints = $eloConfig['average_points'];
        $this->oddsMultiplier = $eloConfig['odds_multiplier'];
        $this->scalingFactor = $eloConfig['scaling_factor'];
        $this->homeAdjustment = $eloConfig['home_adjustment'];
        $this->spreadAdjustment = $eloConfig['spread_adjustment'];
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
        return log($pointDiff + 1) * (2.2 / (($eloDiff * 0.001) + 2.2)) * $this->scalingFactor;
    }

    public function calculateExpectedPointSpread($eloDiff): float|int
    {
        return $eloDiff / 25 * $this->spreadAdjustment; // Use spread adjustment from config
    }

    public function calculateHomeFieldAdjustment($homeTeam, $awayTeam, $distance, $neutralSite = false, $noFans = false): float|int
    {
        if ($neutralSite) {
            return $this->distanceFactor * ($distance / 1000);
        }

        $baseAdvantage = $noFans ? 33 : $this->homeFieldAdvantage;
        return $baseAdvantage + $this->distanceFactor * ($distance / 1000) + $this->homeAdjustment;
    }

    public function getCurrentSeasonWinningRecord($teamId): float
    {
        $totalGames = NflTeamSchedule::where('season_type', 'Regular')
            ->where(function ($query) use ($teamId) {
                $query->where('team_id_home', $teamId)
                    ->orWhere('team_id_away', $teamId);
            })
            ->count();

        $wins = NflTeamSchedule::where('season_type', 'Regular')
            ->where(function ($query) use ($teamId) {
                $query->where(function ($query) use ($teamId) {
                    $query->where('team_id_home', $teamId)
                        ->where('home_result', 'W');
                })->orWhere(function ($query) use ($teamId) {
                    $query->where('team_id_away', $teamId)
                        ->where('away_result', 'W');
                });
            })
            ->count();

        return $totalGames > 0 ? $wins / $totalGames : 0.5; // Default to 0.5 if no games played
    }

}
