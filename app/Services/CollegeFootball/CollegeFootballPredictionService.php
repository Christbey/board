<?php

namespace App\Services\CollegeFootball;

use App\Models\CollegeFootballEloRating;
use App\Models\CollegeFootballFpiRating;
use App\Models\CollegeFootballGame;
use App\Models\CollegeFootballPregame;
use App\Models\NcaaOdds;
use App\Models\NcaaScore;
use App\Models\NcaaTeam;
use DB;

class CollegeFootballPredictionService
{
    public function getWeeks(int $season)
    {
        return CollegeFootballGame::select('week')->distinct()->orderBy('week')
            ->where('season', $season)
            ->get();
    }

    public function getGamesByWeek($week, $year)
    {
        // Assuming you're fetching games from a database
        return DB::table('college_football_games')
            ->where('week', $week)
            ->where('season', $year)
            ->where('start_date', '>=', now()->subDay()) // Get games from the last 24 hours
            ->orderBy('start_date', 'asc')
            ->get();
    }


    protected function hasRatings($game)
    {
        $homeFpi = CollegeFootballFpiRating::where('team_id', $game->home_team_id)
            ->where('year', $game->season)
            ->first();
        $awayFpi = CollegeFootballFpiRating::where('team_id', $game->away_team_id)
            ->where('year', $game->season)
            ->first();

        $homeElo = $game->home_pregame_elo ?? CollegeFootballEloRating::where('team_id', $game->home_team_id)
            ->where('year', $game->season)
            ->value('elo');
        $awayElo = $game->away_pregame_elo ?? CollegeFootballEloRating::where('team_id', $game->away_team_id)
            ->where('year', $game->season)
            ->value('elo');

        return
            ($homeFpi && $awayFpi) ||
            ($homeElo && $awayElo) ||
            ($homeFpi && $awayElo) ||
            ($awayFpi && $homeElo);
    }

    public function getPredictionDetails(int $gameId)
    {
        $game = CollegeFootballGame::findOrFail($gameId);

        $homeFpi = CollegeFootballFpiRating::where('team_id', $game->home_team_id)
            ->where('year', $game->season)
            ->value('fpi');

        $awayFpi = CollegeFootballFpiRating::where('team_id', $game->away_team_id)
            ->where('year', $game->season)
            ->value('fpi');

        $homeElo = $game->home_pregame_elo ?? CollegeFootballEloRating::where('team_id', $game->home_team_id)
            ->where('year', $game->season)
            ->value('elo');

        $awayElo = $game->away_pregame_elo ?? CollegeFootballEloRating::where('team_id', $game->away_team_id)
            ->where('year', $game->season)
            ->value('elo');

        $pregameData = CollegeFootballPregame::where('game_id', $gameId)->first();

        $homeNcaaTeam = NcaaTeam::where('cf_team_id', $game->home_team_id)->first();
        $awayNcaaTeam = NcaaTeam::where('cf_team_id', $game->away_team_id)->first();

        if (!$homeNcaaTeam || !$awayNcaaTeam) {
            return ['error' => 'Matching NCAA teams not found'];
        }

        $odds = NcaaOdds::where('home_team_id', $homeNcaaTeam->id)
            ->where('away_team_id', $awayNcaaTeam->id)
            ->latest('commence_time')
            ->first();

        $homeWinProb = $this->calculateHomeWinProb($odds, $pregameData);

        $scores = NcaaScore::where('home_team_id', $homeNcaaTeam->id)
            ->where('away_team_id', $awayNcaaTeam->id)
            ->latest('commence_time')
            ->first();

        $actualWinner = $this->determineWinner($game, $scores, $homeFpi, $awayFpi, $homeElo, $awayElo);

        $prediction = [
            'winner' => $actualWinner,
            'home_team' => $game->home_team,
            'away_team' => $game->away_team,
            'home_fpi' => $homeFpi ?? 'N/A',
            'away_fpi' => $awayFpi ?? 'N/A',
            'home_elo' => $homeElo ?? 'N/A',
            'away_elo' => $awayElo ?? 'N/A',
            'spread' => $pregameData->spread ?? 'N/A',
            'home_win_prob' => $homeWinProb,
            'odds' => $this->formatOddsData($odds),
            'actual_home_score' => $scores->home_team_score ?? 'N/A',
            'actual_away_score' => $scores->away_team_score ?? 'N/A',
            'game_completed' => $scores->completed ?? 0,
            'hypothetical_spread' => $this->calculateHypotheticalSpread($game->home_team_id, $game->away_team_id, $game->season),
        ];

        return $prediction;
    }

    protected function calculateHomeWinProb($odds, $pregameData)
    {
        if ($odds && isset($odds->h2h_home_price) && $odds->h2h_home_price !== 0) {
            if ($odds->h2h_home_price > 0) {
                return 100 / ($odds->h2h_home_price + 100) * 100;
            } else {
                return -$odds->h2h_home_price / (-$odds->h2h_home_price + 100) * 100;
            }
        } elseif ($pregameData) {
            return $pregameData->home_win_prob * 100;
        }

        return 'N/A';
    }

    protected function determineWinner($game, $scores, $homeFpi, $awayFpi, $homeElo, $awayElo)
    {
        if ($scores && $scores->completed) {
            return $scores->home_team_score > $scores->away_team_score ? $game->home_team : $game->away_team;
        }

        return $this->predictWinner($game->home_team_id, $game->away_team_id, $game->season, $game->home_team, $game->away_team);
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
            $combinedSpread = ($fpiSpread + $eloSpread) / 1.2;
        } elseif ($fpiSpread) {
            $combinedSpread = $fpiSpread / .5;
        } elseif ($eloSpread) {
            $combinedSpread = $eloSpread;
        } else {
            $combinedSpread = 0; // No data to calculate spread
        }

        return round($combinedSpread, 1); // Return the spread rounded to 1 decimal place
    }

    protected function formatOddsData($odds)
    {
        if (!$odds) {
            return null;
        }

        return [
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
}
