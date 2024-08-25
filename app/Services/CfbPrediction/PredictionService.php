<?php

namespace App\Services\CfbPrediction;

use App\Models\CollegeFootballFpiRating;
use App\Models\CollegeFootballGame;
use App\Models\CollegeFootballPregame;
use App\Models\NcaaOdds;
use App\Models\NcaaScore;
use Illuminate\Support\Carbon;

class PredictionService
{
    public function generatePrediction(CollegeFootballGame $game): array
    {
        // Fetch FPI ratings for home and away teams
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

        // Access NCAA teams directly from the CollegeFootballTeam relationships
        $homeNcaaTeam = $game->homeTeam->ncaaTeam;
        $awayNcaaTeam = $game->awayTeam->ncaaTeam;

        // Check if NCAA teams exist
        if (!$homeNcaaTeam || !$awayNcaaTeam) {
            return [];
        }

        // Fetch the latest odds
        $odds = NcaaOdds::where('home_team_id', $homeNcaaTeam->id)
            ->where('away_team_id', $awayNcaaTeam->id)
            ->latest('commence_time')
            ->first();

        if (!$odds) {
            return [];
        }

        $homeAdvantage = $this->calculateHomeAdvantage($game);
        $eloImpact = $this->calculateEloImpact($game);
        $spreadImpact = $this->calculateSpreadImpact($odds);

        $homeScore = $homeFpi->fpi + $homeAdvantage + $eloImpact['home'] + $spreadImpact['home'];
        $awayScore = $awayFpi->fpi + $eloImpact['away'] + $spreadImpact['away'];

        // Calculate home win probability from odds if available, else use pregame data
        $homeWinProb = $odds->h2h_home_price > 0
            ? 100 / ($odds->h2h_home_price + 100)
            : -$odds->h2h_home_price / (-$odds->h2h_home_price + 100);

        $homeWinProb *= 100; // Convert to percentage
        $homeWinProb = $homeWinProb ?: $pregameData->home_win_prob; // Fallback if calculated probability is zero

        // Determine the predicted winner
        $predictedWinner = $homeWinProb > 50 ? $game->home_team : $game->away_team;

        // Fetch the latest scores
        $scores = NcaaScore::where('home_team_id', $homeNcaaTeam->id)
            ->where('away_team_id', $awayNcaaTeam->id)
            ->where('commence_time', $game->start_date) // Ensure scores match the correct game
            ->latest('commence_time')
            ->first();

        // If the game is completed, show the actual winner
        if ($scores && $scores->completed) {
            $predictedWinner = $scores->home_team_score > $scores->away_team_score
                ? $game->home_team
                : $game->away_team;
        }

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
            'home_win_prob' => $homeWinProb,
            'odds' => $odds,
            'actual_home_score' => $scores->home_team_score ?? 'N/A',
            'actual_away_score' => $scores->away_team_score ?? 'N/A',
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

    private function calculateSpreadImpact(NcaaOdds $odds): array
    {
        $spreadScalingFactor = 0.1;

        return [
            'home' => $odds->spread_home_point * $spreadScalingFactor,
            'away' => $odds->spread_away_point * $spreadScalingFactor,
        ];
    }
}
