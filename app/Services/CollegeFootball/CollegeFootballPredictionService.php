<?php

namespace App\Services\CollegeFootball;

use App\Models\CollegeFootballEloRating;
use App\Models\CollegeFootballFpiRating;
use App\Models\CollegeFootballGame;
use App\Models\CollegeFootballPregame;
use App\Models\CollegeFootballSpRating;
use App\Models\CollegeFootballTalent;
use App\Models\CollegeFootballTeam;
use App\Models\NcaaOdds;
use App\Models\NcaaScore;
use App\Models\NcaaTeam;

class CollegeFootballPredictionService
{
    public function getWeeks(int $season)
    {
        return CollegeFootballGame::select('week')
            ->distinct()
            ->orderBy('week')
            ->where('completed', false) // Add this condition to exclude completed games

            ->where('season', $season)
            ->where('start_date', '>=', now()->subDay())
            ->get();
    }

    public function getGamesByWeek($week, $year)
    {
        return CollegeFootballGame::where('week', $week)
            ->where('season', $year)
            ->where('start_date', '>=', now())
            ->where('home_division', 'fbs')
            ->where('completed', false) // This should filter out completed games
            ->orderBy('start_date', 'asc')
            ->get();
    }

    public function getTalentRating(mixed $home_team_id, mixed $season)
    {
        return CollegeFootballTalent::where('team_id', $home_team_id)
            ->where('year', $season)
            ->value('talent');
    }

    public function getSpRating(mixed $home_team_id, mixed $season)
    {
        return CollegeFootballSpRating::where('team_id', $home_team_id)
            ->where('year', $season)
            ->value('rating');
    }


    protected function hasRatings($game)
    {
        $homeFpi = $this->getFpiRating($game->home_team_id, $game->season);
        $awayFpi = $this->getFpiRating($game->away_team_id, $game->season);

        $homeElo = $game->home_pregame_elo ?? $this->getEloRating($game->home_team_id, $game->season);
        $awayElo = $game->away_pregame_elo ?? $this->getEloRating($game->away_team_id, $game->season);

        return ($homeFpi && $awayFpi) || ($homeElo && $awayElo) || ($homeFpi && $awayElo) || ($awayFpi && $homeElo);
    }

    public function getPredictionDetails(int $gameId)
    {
        $game = CollegeFootballGame::findOrFail($gameId);

        $homeFpi = $this->getFpiRating($game->home_team_id, $game->season);
        $awayFpi = $this->getFpiRating($game->away_team_id, $game->season);

        $homeTeamSchool = CollegeFootballTeam::where('id', $game->home_team_id)->value('school');
        $awayTeamSchool = CollegeFootballTeam::where('id', $game->away_team_id)->value('school');

        $homeElo = $game->home_pregame_elo ?? $this->getEloRating($game->home_team_id, $game->season);
        $awayElo = $game->away_pregame_elo ?? $this->getEloRating($game->away_team_id, $game->season);

        $homeTalent = CollegeFootballTalent::where('team_id', $game->home_team_id)
            ->where('year', $game->season)
            ->value('talent');

        $awayTalent = CollegeFootballTalent::where('team_id', $game->away_team_id)
            ->where('year', $game->season)
            ->value('talent');

        $homeSpRating = CollegeFootballSpRating::where('team_id', $game->home_team_id)
            ->where('year', $game->season)
            ->value('rating');

        $awaySpRating = CollegeFootballSpRating::where('team_id', $game->away_team_id)
            ->where('year', $game->season)
            ->value('rating');

        // Retrieve team colors
        $homeTeamColor = CollegeFootballTeam::where('id', $game->home_team_id)->value('color');
        $awayTeamColor = CollegeFootballTeam::where('id', $game->away_team_id)->value('color');

        $hypotheticalSpread = $this->calculateHypotheticalSpread(
            $homeFpi,
            $awayFpi,
            $homeElo,
            $awayElo,
            $homeTalent,
            $awayTalent,
            $homeSpRating,
            $awaySpRating
        );

        $pregameData = CollegeFootballPregame::where('game_id', $gameId)->first();

        $homeNcaaTeam = NcaaTeam::where('cf_team_id', $game->home_team_id)->first();
        $awayNcaaTeam = NcaaTeam::where('cf_team_id', $game->away_team_id)->first();

        if (!$homeNcaaTeam || !$awayNcaaTeam) {
            return ['error' => 'Matching NCAA teams not found'];
        }

        $odds = $this->getOdds($game->ncaa_odds_id, $homeNcaaTeam->id, $awayNcaaTeam->id);

        $homeWinProb = $this->calculateHomeWinProb($odds, $pregameData);

        // Now retrieve the scores
        $scores = NcaaScore::where('home_team_id', $homeNcaaTeam->id)
            ->where('away_team_id', $awayNcaaTeam->id)
            ->latest('commence_time')
            ->first();

        // Use the scores after they are defined
        if ($scores) {
            $actualHomeScore = $scores->home_team_score ?? $game->home_points ?? 'N/A';
            $actualAwayScore = $scores->away_team_score ?? $game->away_points ?? 'N/A';
            $gameCompleted = $scores->completed ?? ($game->home_points !== null && $game->away_points !== null) ? 1 : 0;
        } else {
            $actualHomeScore = $game->home_points ?? 'N/A';
            $actualAwayScore = $game->away_points ?? 'N/A';
            $gameCompleted = ($game->home_points !== null && $game->away_points !== null) ? 1 : 0;
        }

        $actualWinner = $this->determineWinner($game, $scores, $homeFpi, $awayFpi, $homeElo, $awayElo);

        return [
            'winner' => $actualWinner,
            'home_team_school' => $homeTeamSchool,
            'away_team_school' => $awayTeamSchool,
            'home_team' => $game->home_team,
            'away_team' => $game->away_team,
            'home_fpi' => $homeFpi ?? 'N/A',
            'away_fpi' => $awayFpi ?? 'N/A',
            'home_elo' => $homeElo ?? 'N/A',
            'away_elo' => $awayElo ?? 'N/A',
            'spread' => $pregameData->spread ?? 'N/A',
            'home_win_prob' => $homeWinProb,
            'odds' => $this->formatOddsData($odds),
            'actual_home_score' => $actualHomeScore,
            'actual_away_score' => $actualAwayScore,
            'game_completed' => $gameCompleted,
            'hypothetical_spread' => $hypotheticalSpread,
            'home_team_color' => $homeTeamColor,
            'away_team_color' => $awayTeamColor,
        ];
    }

    public function getFpiRating($teamId, $year)
    {
        return CollegeFootballFpiRating::where('team_id', $teamId)
            ->where('year', $year)
            ->value('fpi');
    }

    public function getEloRating($teamId, $year)
    {
        return CollegeFootballEloRating::where('team_id', $teamId)
            ->where('year', $year)
            ->value('elo');
    }

    private function getOdds($ncaaOddsId, $homeTeamId, $awayTeamId)
    {
        $odds = NcaaOdds::find($ncaaOddsId);

        if (!$odds) {
            $odds = NcaaOdds::where('home_team_id', $homeTeamId)
                ->where('away_team_id', $awayTeamId)
                ->latest('commence_time')
                ->first();
        }

        return $odds;
    }

    protected function calculateHomeWinProb($odds, $pregameData): float|int|string
    {
        if ($odds && isset($odds->h2h_home_price) && $odds->h2h_home_price !== 0) {
            $homeWinProb = $odds->h2h_home_price > 0
                ? 100 / ($odds->h2h_home_price + 10) * 100
                : -$odds->h2h_home_price / (-$odds->h2h_home_price + 100) * 100;

            return round($homeWinProb, 2);
        } elseif ($pregameData) {
            return round($pregameData->home_win_prob * 100, 2);
        }

        return 'N/A';
    }

    protected function determineWinner($game, $scores, $homeFpi, $awayFpi, $homeElo, $awayElo)
    {
        if ($scores && $scores->completed) {
            return $scores->home_team_score > $scores->away_team_score ? $game->home_team : $game->away_team;
        }

        return $this->predictWinner($homeFpi, $awayFpi, $homeElo, $awayElo, $game->home_team, $game->away_team);
    }

    private function predictWinner($homeFpi, $awayFpi, $homeElo, $awayElo, $homeTeamName, $awayTeamName): string
    {
        $fpiWinProb = ($homeFpi && $awayFpi) ? 1 / (1 + pow(10, -($homeFpi - $awayFpi) / 10)) : null;
        $eloWinProb = ($homeElo && $awayElo) ? 1 / (1 + pow(10, -($homeElo - $awayElo) / 400)) : null;

        $combinedWinProb = ($fpiWinProb !== null && $eloWinProb !== null)
            ? ($fpiWinProb + $eloWinProb) / 2
            : ($fpiWinProb ?? $eloWinProb);

        return $combinedWinProb > 0.5 ? $homeTeamName : $awayTeamName;
    }

    public function calculateHypotheticalSpread($homeFpi, $awayFpi, $homeElo, $awayElo, $homeTalent, $awayTalent, $homeSpRating, $awaySpRating): float
    {
        $fpiSpread = $homeFpi && $awayFpi ? ($homeFpi - $awayFpi) / 2 : 0;
        $eloSpread = $homeElo && $awayElo ? ($homeElo - $awayElo) / 25 : 0;
        $talentSpread = $homeTalent && $awayTalent ? ($homeTalent - $awayTalent) / 100 : 0;
        $spRatingSpread = $homeSpRating && $awaySpRating ? ($homeSpRating - $awaySpRating) / 100 : 0; // Adjust divisor as necessary

        return round(($fpiSpread + $eloSpread + $talentSpread + $spRatingSpread) / 1.4, 2);
    }


    protected function formatOddsData($odds)
    {
        if (!$odds) {
            return [
                'home_team_id' => null,
                'away_team_id' => null,
                'home_moneyline' => null,
                'away_moneyline' => null,
                'home_spread' => [
                    'points' => null,
                    'price' => null,
                ],
                'away_spread' => [
                    'points' => null,
                    'price' => null,
                ],
                'total' => [
                    'over' => [
                        'points' => null,
                        'price' => null,
                    ],
                    'under' => [
                        'points' => null,
                        'price' => null,
                    ],
                ],
                'bookmaker' => null,
                'commence_time' => null,
            ];
        }

        return [
            'home_team_id' => $odds->home_team_id,
            'away_team_id' => $odds->away_team_id,
            'home_moneyline' => $odds->h2h_home_price,
            'away_moneyline' => $odds->h2h_away_price,
            'home_spread' => [
                'points' => $odds->spread_home_point,
                'price' => $odds->spread_home_price,
            ],
            'away_spread' => [
                'points' => $odds->spread_away_point,
                'price' => $odds->spread_away_price,
            ],
            'total' => [
                'over' => [
                    'points' => $odds->total_over_point,
                    'price' => $odds->total_over_price,
                ],
                'under' => [
                    'points' => $odds->total_under_point,
                    'price' => $odds->total_under_price,
                ],
            ],
            'bookmaker' => $odds->bookmaker_key,
            'commence_time' => $odds->commence_time,
        ];
    }
}
