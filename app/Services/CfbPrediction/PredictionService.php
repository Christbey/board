<?php

namespace App\Services\CfbPrediction;

use App\Models\CollegeFootballFpiRating;
use App\Models\CollegeFootballGame;
use App\Models\CollegeFootballPregame;

class PredictionService
{
    public function generatePrediction(CollegeFootballGame $game): array
    {
        $homeFpi = CollegeFootballFpiRating::where('team_id', $game->home_team_id)
            ->where('year', $game->season)
            ->first();
        $awayFpi = CollegeFootballFpiRating::where('team_id', $game->away_team_id)
            ->where('year', $game->season)
            ->first();
        $pregameData = CollegeFootballPregame::where('game_id', $game->id)->first();

        if (!$homeFpi || !$awayFpi || !$pregameData) {
            return [];
        }

        $homeAdvantage = $this->calculateHomeAdvantage($game);
        $eloImpact = $this->calculateEloImpact($game);
        $spreadImpact = $this->calculateSpreadImpact($pregameData);

        $homeScore = $homeFpi->fpi + $homeAdvantage + $eloImpact['home'] + $spreadImpact['home'];
        $awayScore = $awayFpi->fpi + $eloImpact['away'] + $spreadImpact['away'];

        $predictedWinner = $homeScore > $awayScore ? $game->home_team : $game->away_team;

        return [
            'predicted_winner' => $predictedWinner,
            'home_team' => $game->home_team,
            'away_team' => $game->away_team,
            'home_fpi' => $homeFpi->fpi,
            'away_fpi' => $awayFpi->fpi,
            'home_score' => $homeScore,
            'away_score' => $awayScore,
            'home_elo' => $game->home_pregame_elo,
            'away_elo' => $game->away_pregame_elo,
            'elo_impact' => $eloImpact,
            'spread' => $pregameData->spread,
            'home_win_prob' => $pregameData->home_win_prob,
        ];
    }

    private function calculateHomeAdvantage(CollegeFootballGame $game): float
    {
        return $game->neutral_site ? 0 : 2.5;
    }

    private function calculateEloImpact(CollegeFootballGame $game): array
    {
        $eloDifference = $game->home_pregame_elo - $game->away_pregame_elo;
        $scalingFactor = 0.01;

        return [
            'home' => $eloDifference * $scalingFactor,
            'away' => -$eloDifference * $scalingFactor,
        ];
    }

    private function calculateSpreadImpact(CollegeFootballPregame $pregameData): array
    {
        $spreadScalingFactor = 0.5;

        return [
            'home' => $pregameData->spread * $spreadScalingFactor,
            'away' => -$pregameData->spread * $spreadScalingFactor,
        ];
    }
}
