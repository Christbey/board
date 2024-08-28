<?php

namespace App\Http\Controllers;

use App\Models\CollegeFootballEloRating;
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
    public function index(Request $request)
    {
        $weeks = CollegeFootballGame::select('week')->distinct()->orderBy('week')
            ->where('season', 2024)
            ->get();
        $games = [];

        if ($request->has('week') && $request->week) {
            $games = CollegeFootballGame::where('week', $request->week)
                ->where('season', 2024)
                ->get()
                ->filter(function ($game) {
                    // Fetch FPI Ratings for Home and Away Teams
                    $homeFpi = CollegeFootballFpiRating::where('team_id', $game->home_team_id)
                        ->where('year', $game->season)
                        ->first();
                    $awayFpi = CollegeFootballFpiRating::where('team_id', $game->away_team_id)
                        ->where('year', $game->season)
                        ->first();

                    // Check for ELO Ratings
                    $homeElo = $game->home_pregame_elo ?? CollegeFootballEloRating::where('team_id', $game->home_team_id)
                        ->where('year', $game->season)
                        ->value('elo');
                    $awayElo = $game->away_pregame_elo ?? CollegeFootballEloRating::where('team_id', $game->away_team_id)
                        ->where('year', $game->season)
                        ->value('elo');

                    // Include the game if either FPI or ELO ratings are available for both teams
                    return
                        ($homeFpi && $awayFpi) || // Both teams have FPI ratings
                        ($homeElo && $awayElo) || // Both teams have ELO ratings
                        ($homeFpi && $awayElo) || // Home team has FPI, away team has ELO
                        ($awayFpi && $homeElo);   // Away team has FPI, home team has ELO
                });
        }

        return view('predict.index', compact('weeks', 'games'));
    }

    public function showPrediction($gameId)
    {
        $game = CollegeFootballGame::findOrFail($gameId);
        $hypotheticalSpread = $this->calculateHypotheticalSpread($game->home_team_id, $game->away_team_id, $game->season);

        // Fetch FPI Ratings for Home and Away Teams
        $homeFpi = CollegeFootballFpiRating::where('team_id', $game->home_team_id)
            ->where('year', $game->season)
            ->value('fpi');

        $awayFpi = CollegeFootballFpiRating::where('team_id', $game->away_team_id)
            ->where('year', $game->season)
            ->value('fpi');


        // Check for ELO Ratings, use fallback if pregame ELO is not available
        $homeElo = $game->home_pregame_elo ?? CollegeFootballEloRating::where('team_id', $game->home_team_id)
            ->where('year', $game->season)
            ->value('elo');
        $awayElo = $game->away_pregame_elo ?? CollegeFootballEloRating::where('team_id', $game->away_team_id)
            ->where('year', $game->season)
            ->value('elo');

        $pregameData = CollegeFootballPregame::where('game_id', $gameId)->first();


        // Ensure at least one of the following is true: FPI or ELO ratings are available

        // Fetch matching NCAA teams based on the `cf_team_id` field in `NcaaTeam`
        $homeNcaaTeam = NcaaTeam::where('cf_team_id', $game->home_team_id)->first();
        $awayNcaaTeam = NcaaTeam::where('cf_team_id', $game->away_team_id)->first();

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

        // Calculate home win probability from odds if available, else use pregame data
        if ($odds && isset($odds->h2h_home_price) && $odds->h2h_home_price !== 0) {
            if ($odds->h2h_home_price > 0) {
                $homeWinProb = 100 / ($odds->h2h_home_price + 100);
            } else {
                $homeWinProb = -$odds->h2h_home_price / (-$odds->h2h_home_price + 100);
            }

            $homeWinProb = $homeWinProb * 100; // Convert to percentage
        } else if ($pregameData) {
            // Fallback to pregame data if odds are not available or invalid
            $homeWinProb = $pregameData->home_win_prob * 100;
        } else {
            $homeWinProb = 'N/A'; // If no odds or pregame data, return N/A
        }

        Log::info('Querying scores for game:', [
            'home_team_id' => $game->home_team_id,
            'away_team_id' => $game->away_team_id
        ]);

        // Fetch the latest scores based on NCAA teams
        $scores = NcaaScore::where('home_team_id', $homeNcaaTeam->id)
            ->where('away_team_id', $awayNcaaTeam->id)
            ->latest('commence_time')
            ->first();

        Log::info('Scores retrieved:', ['scores' => $scores]);

        // Determine the winner
        if ($scores && $scores->completed) {
            // Use the actual scores to determine the winner
            $actualWinner = $scores->home_team_score > $scores->away_team_score ? $game->home_team : $game->away_team;
        } else {
            // Use the new predictWinner method if the game isn't completed
            $predictedWinner = $this->predictWinner($game->home_team_id, $game->away_team_id, $game->season, $game->home_team, $game->away_team);
            $actualWinner = $predictedWinner === 'home' ? $game->home_team : $game->away_team;
        }


        // Clean up the odds data
        $oddsData = null;
        if ($odds) {
            $oddsData = [
                'home_team_id' => $odds->home_team_id,
                'away_team_id' => $odds->away_team_id,
                'home_moneyline' => $odds->h2h_home_price,
                'away_moneyline' => $odds->h2h_away_price,
                'home_spread' => [
                    'points' => $odds->spread_home_point,
                    'price' => $odds->spread_home_price
                ],
                'away_spread' => [
                    'points' => $odds->spread_away_point,
                    'price' => $odds->spread_away_price
                ],
                'total' => [
                    'over' => [
                        'points' => $odds->total_over_point,
                        'price' => $odds->total_over_price
                    ],
                    'under' => [
                        'points' => $odds->total_under_point,
                        'price' => $odds->total_under_price
                    ]
                ],
                'bookmaker' => $odds->bookmaker_key,
                'commence_time' => $odds->commence_time,
            ];
        }

        // Adjust the prediction array to include the cleaned-up odds
        $prediction = [
            'winner' => $scores && $scores->completed ? $actualWinner : $predictedWinner,
            'home_team' => $game->home_team,
            'away_team' => $game->away_team,
            'home_fpi' => $homeFpi ?? 'N/A',
            'away_fpi' => $awayFpi ?? 'N/A',
            'home_elo' => $homeElo ?? 'N/A',
            'away_elo' => $awayElo ?? 'N/A',
            'spread' => $pregameData->spread ?? 'N/A',
            'home_win_prob' => $homeWinProb,
            'odds' => $oddsData,
            'actual_home_score' => $scores->home_team_score ?? 'N/A',
            'actual_away_score' => $scores->away_team_score ?? 'N/A',
            'game_completed' => $scores->completed ?? 0,
            'hypothetical_spread' => $hypotheticalSpread,
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

    private function predictWinner(int $homeTeamId, int $awayTeamId, int $season, string $homeTeamName, string $awayTeamName): string
    {
        // Fetch FPI Ratings for Home and Away Teams
        $homeFpi = CollegeFootballFpiRating::where('team_id', $homeTeamId)
            ->where('year', $season)
            ->first();
        $awayFpi = CollegeFootballFpiRating::where('team_id', $awayTeamId)
            ->where('year', $season)
            ->first();

        // Check for ELO Ratings, use fallback if pregame ELO is not available
        $homeElo = CollegeFootballEloRating::where('team_id', $homeTeamId)
            ->where('year', $season)
            ->value('elo');
        $awayElo = CollegeFootballEloRating::where('team_id', $awayTeamId)
            ->where('year', $season)
            ->value('elo');

        // Calculate FPI Win Probability if both FPI ratings are available
        if ($homeFpi && $awayFpi) {
            $fpiDifference = $homeFpi->fpi - $awayFpi->fpi;
            $fpiWinProb = 1 / (1 + pow(10, -$fpiDifference / 10));
        } else {
            $fpiWinProb = null; // FPI data is missing
        }

        // Calculate ELO Win Probability if both ELO ratings are available
        if ($homeElo && $awayElo) {
            $eloDifference = $homeElo - $awayElo;
            $eloWinProb = 1 / (1 + pow(10, -$eloDifference / 400));
        } else {
            $eloWinProb = null; // ELO data is missing
        }

        // Combine FPI and ELO probabilities if both are available
        if ($fpiWinProb !== null && $eloWinProb !== null) {
            $combinedWinProb = ($fpiWinProb + $eloWinProb) / 2;
        } elseif ($fpiWinProb !== null) {
            $combinedWinProb = $fpiWinProb;
        } elseif ($eloWinProb !== null) {
            $combinedWinProb = $eloWinProb;
        } else {
            return 'unknown'; // No valid data to predict the winner
        }

        // Predict the winner
        return $combinedWinProb > 0.5 ? $homeTeamName : $awayTeamName;
    }

    private function calculateHypotheticalSpread(int $homeTeamId, int $awayTeamId, int $season): float
    {
        // Fetch FPI Ratings for Home and Away Teams
        $homeFpi = CollegeFootballFpiRating::where('team_id', $homeTeamId)
            ->where('year', $season)
            ->first();
        $awayFpi = CollegeFootballFpiRating::where('team_id', $awayTeamId)
            ->where('year', $season)
            ->first();

        // Check for ELO Ratings
        $homeElo = CollegeFootballEloRating::where('team_id', $homeTeamId)
            ->where('year', $season)
            ->value('elo');
        $awayElo = CollegeFootballEloRating::where('team_id', $awayTeamId)
            ->where('year', $season)
            ->value('elo');

        // Calculate FPI-based spread if both FPI ratings are available
        if ($homeFpi && $awayFpi) {
            $fpiDifference = $homeFpi->fpi - $awayFpi->fpi;
            $fpiSpread = $fpiDifference / 2; // Example: divide the FPI difference by 2 to create a spread
        } else {
            $fpiSpread = 0; // No FPI data available
        }

        // Calculate ELO-based spread if both ELO ratings are available
        if ($homeElo && $awayElo) {
            $eloDifference = $homeElo - $awayElo;
            $eloSpread = $eloDifference / 25; // Example: divide the ELO difference by 25 to create a spread
        } else {
            $eloSpread = 0; // No ELO data available
        }

        // Combine the spreads to form a final hypothetical spread
        if ($fpiSpread && $eloSpread) {
            $combinedSpread = ($fpiSpread + $eloSpread) / 2;
        } elseif ($fpiSpread) {
            $combinedSpread = $fpiSpread;
        } elseif ($eloSpread) {
            $combinedSpread = $eloSpread;
        } else {
            $combinedSpread = 0; // No data to calculate spread
        }

        return round($combinedSpread, 1); // Return the spread rounded to 1 decimal place
    }
}