<?php

namespace App\Http\Controllers;

use App\Models\CollegeFootballFpiRating;
use App\Models\CollegeFootballGame;
use App\Models\CollegeFootballPregame;
use App\Models\NcaaTeam;
use App\Models\NcaaOdds;
use App\Models\NcaaScore;
use Illuminate\Http\Request;
use Log;

class CollegeFootballPredictionController extends Controller
{
// Inside the CollegeFootballPredictionController

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

        // Fetch matching NCAA teams
        $homeNcaaTeam = $this->getMatchingNcaaTeam($game->home_team);
        $awayNcaaTeam = $this->getMatchingNcaaTeam($game->away_team);

        if (!$homeNcaaTeam || !$awayNcaaTeam) {
            return view('predict.game', [
                'error' => 'Matching NCAA teams not found'
            ]);
        }

        // Fetch the latest odds
        $odds = NcaaOdds::where('home_team_id', $homeNcaaTeam->id)
            ->where('away_team_id', $awayNcaaTeam->id)
            ->latest('commence_time')
            ->first();

        if (!$odds) {
            return view('predict.game', [
                'error' => 'No odds found for the selected game'
            ]);
        }

        // Calculate home win probability from odds if available, else use pregame data
        $homeWinProb = $odds->h2h_home_price > 0
            ? 100 / ($odds->h2h_home_price + 100)
            : -$odds->h2h_home_price / (-$odds->h2h_home_price + 100);

        $homeWinProb = $homeWinProb * 100; // Convert to percentage
        $homeWinProb = $homeWinProb ?: $pregameData->home_win_prob; // Fallback if calculated probability is zero

        // Fetch the latest scores
        $scores = NcaaScore::where('home_team_id', $homeNcaaTeam->id)
            ->where('away_team_id', $awayNcaaTeam->id)
            ->latest('commence_time')
            ->first();

        // Determine the predicted winner based on the scores or the probability
        if ($scores && $scores->completed) {
            $actualWinner = $scores->home_team_score > $scores->away_team_score ? $game->home_team : $game->away_team;
        } else {
            $predictedWinner = $homeWinProb > 50 ? $game->home_team : $game->away_team;
        }

        // Adjust the prediction array to include the scores
        $prediction = [
            'winner' => $scores && $scores->completed ? $actualWinner : $predictedWinner,
            'home_team' => $game->home_team,
            'away_team' => $game->away_team,

            'home_fpi' => $homeFpi->fpi,
            'away_fpi' => $awayFpi->fpi,
            'home_elo' => $game->home_pregame_elo,
            'away_elo' => $game->away_pregame_elo,
            'spread' => $pregameData->spread,
            'home_win_prob' => $homeWinProb,
            'odds' => $odds,
            'actual_home_score' => $scores->home_team_score ?? 'N/A',
            'actual_away_score' => $scores->away_team_score ?? 'N/A',
            'game_completed' => $scores->completed ?? 0,
        ];

        return view('predict.game', compact('prediction'));
    }

    private function getMatchingNcaaTeam($collegeTeamName)
    {
        return NcaaTeam::where('name', 'like', "%{$collegeTeamName}%")
            ->orWhere('team_mascot', 'like', "%{$collegeTeamName}%")
            ->orWhere('abbreviation', 'like', "%{$collegeTeamName}%")
            ->first();
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
        $spreadScalingFactor = 0.5;

        return [
            'home' => $odds->spread_home_point * $spreadScalingFactor,
            'away' => $odds->spread_away_point * $spreadScalingFactor,
        ];
    }

    private function calculateOddsImpact(NcaaOdds $odds): array
    {
        $oddsScalingFactor = 0.1;

        return [
            'home' => $odds->spread_home_point * $oddsScalingFactor,
            'away' => $odds->spread_away_point * $oddsScalingFactor,
        ];
    }
}
