<?php

namespace App\Http\Controllers;

use App\Models\CollegeFootballFpiRating;
use App\Models\CollegeFootballGame;
use App\Models\CollegeFootballPregame;
use Illuminate\Http\Request;

class CollegeFootballPredictionController extends Controller
{
    public function showPrediction($gameId)
    {
        $game = CollegeFootballGame::findOrFail($gameId);
        $homeFpi = CollegeFootballFpiRating::where('team_id', $game->home_team_id)
            ->where('year', $game->season)
            ->first();
        $awayFpi = CollegeFootballFpiRating::where('team_id', $game->away_team_id)
            ->where('year', $game->season)
            ->first();

        $pregameData = CollegeFootballPregame::where('game_id', $gameId)->first();

        if (!$homeFpi || !$awayFpi || !$pregameData) {
            return view('predict.game', [
                'error' => 'FPI ratings or pregame data not found for one or both teams'
            ]);
        }

        // Calculate prediction with Elo and Pregame adjustments
        $homeAdvantage = $this->calculateHomeAdvantage($game);
        $eloImpact = $this->calculateEloImpact($game);
        $spreadImpact = $this->calculateSpreadImpact($pregameData);

        $homeScore = $homeFpi->fpi + $homeAdvantage + $eloImpact['home'] + $spreadImpact['home'];
        $awayScore = $awayFpi->fpi + $eloImpact['away'] + $spreadImpact['away'];

        $predictedWinner = $homeScore > $awayScore ? $game->home_team : $game->away_team;

        $prediction = [
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

        return view('predict.game', compact('prediction'));
    }

    private function calculateHomeAdvantage(CollegeFootballGame $game): float
    {
        // Example: add 2.5 points for the home team if not a neutral site
        return $game->neutral_site ? 0 : 2.5;
    }

    private function calculateEloImpact(CollegeFootballGame $game): array
    {
        // Scale down the Elo impact to prevent overwhelming the FPI score
        $eloDifference = $game->home_pregame_elo - $game->away_pregame_elo;
        $scalingFactor = 0.01;  // Adjust this factor as necessary

        return [
            'home' => $eloDifference * $scalingFactor,
            'away' => -$eloDifference * $scalingFactor,
        ];
    }

    private function calculateSpreadImpact(CollegeFootballPregame $pregameData): array
    {
        // Adjust based on the spread
        // Positive spread means home team is favored
        // Negative spread means away team is favored
        $spreadScalingFactor = 0.5;  // Adjust this factor as necessary

        return [
            'home' => $pregameData->spread * $spreadScalingFactor,
            'away' => -$pregameData->spread * $spreadScalingFactor,
        ];
    }
}
