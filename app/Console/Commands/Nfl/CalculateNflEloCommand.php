<?php

namespace App\Console\Commands\Nfl;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CalculateNflEloCommand extends Command
{
    protected $signature = 'nfl:elo';
    protected $description = 'Calculate Elo ratings based on NFL odds data';
    public function __construct()
    {
        parent::__construct();
    }
    public function handle(): void
    {
        try {
            $games = DB::table('nfl_odds')->select('away_team_id', 'home_team_id', 'h2h_home_price', 'h2h_away_price', 'spread_home_point', 'spread_away_point')->get();
            $eloRatings = $this->initializeEloRatings();

            foreach ($games as $game) {
                $this->processGame($game, $eloRatings);
            }
            $this->saveEloRatings($eloRatings);
        } catch (\Exception $e) {
            Log::error('Failed to calculate NFL Elo ratings: ' . $e->getMessage());
        }
    }

    private function initializeEloRatings(): array
    {
        return DB::table('nfl_rankings')->pluck('season_elo', 'team_id')->toArray();
    }

    private function processGame($game, &$eloRatings): void
    {
        $homeTeam = $game->home_team_id;
        $awayTeam = $game->away_team_id;

        $homeWinProbability = $this->calculateWinProbability($game->h2h_home_price);
        $awayWinProbability = $this->calculateWinProbability($game->h2h_away_price);

        $adjustedHomeProbability = $this->adjustProbabilityForSpread($homeWinProbability, $game->spread_home_point * -1);
        $adjustedAwayProbability = $this->adjustProbabilityForSpread($awayWinProbability, $game->spread_away_point * -1);

        $expectedHome = $this->expectedScore($eloRatings[$homeTeam], $eloRatings[$awayTeam]);
        $expectedAway = $this->expectedScore($eloRatings[$awayTeam], $eloRatings[$homeTeam]);

        $eloRatings[$homeTeam] = $this->updateElo($eloRatings[$homeTeam], $adjustedHomeProbability, $expectedHome);
        $eloRatings[$awayTeam] = $this->updateElo($eloRatings[$awayTeam], $adjustedAwayProbability, $expectedAway);

        Log::info("Updated Elo ratings: Home Team ({$homeTeam}) = {$eloRatings[$homeTeam]}, Away Team ({$awayTeam}) = {$eloRatings[$awayTeam]}");
    }

    private function calculateWinProbability($odds): float|int
    {
        $decimalOdds = $this->convertAmericanToDecimal($odds);
        return 1 / $decimalOdds;
    }

    private function convertAmericanToDecimal($odds): float|int
    {
        return $odds > 0 ? ($odds / 100) + 1 : (100 / abs($odds)) + 1;
    }

    private function adjustProbabilityForSpread($probability, $spread): float|int
    {
        // Adjust the probability based on the spread. This is a simple linear adjustment.
        return $probability * (1 + $spread / 10);
    }

    private function expectedScore($eloA, $eloB): float|int
    {
        return 1 / (1 + pow(10, ($eloB - $eloA) / 400));
    }

    private function updateElo($elo, $actualScore, $expectedScore, $kFactor = 32)
    {
        return $elo + $kFactor * ($actualScore - $expectedScore);
    }

    private function saveEloRatings($eloRatings): void
    {
        foreach ($eloRatings as $team => $elo) {
            try {
                DB::table('nfl_rankings')->where('team_id', $team)->update(['predictive_elo' => $elo]);
            } catch (\Exception $e) {
                Log::error("Failed to update Elo rating for team {$team}: " . $e->getMessage());
            }
        }
    }
}
