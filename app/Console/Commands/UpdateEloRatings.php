<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EloRatingSystem;

class UpdateEloRatings extends Command
{
    protected $signature = 'elo:update';
    protected $description = 'Update Elo ratings for NFL teams';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $teams = ['TeamA', 'TeamB', 'TeamC', 'TeamD'];
        $elo = new EloRatingSystem($teams);

        $games = [
            ['homeTeam' => 'TeamA', 'awayTeam' => 'TeamB', 'homeScore' => 24, 'awayScore' => 17, 'distance' => 1000, 'homeRested' => false, 'awayRested' => false, 'neutralSite' => false, 'noFans' => false, 'isPlayoff' => false, 'homeQbChange' => false, 'awayQbChange' => false],
            ['homeTeam' => 'TeamC', 'awayTeam' => 'TeamD', 'homeScore' => 21, 'awayScore' => 14, 'distance' => 500, 'homeRested' => false, 'awayRested' => false, 'neutralSite' => false, 'noFans' => false, 'isPlayoff' => false, 'homeQbChange' => false, 'awayQbChange' => false],
            ['homeTeam' => 'TeamB', 'awayTeam' => 'TeamA', 'homeScore' => 17, 'awayScore' => 24, 'distance' => 1500, 'homeRested' => false, 'awayRested' => false, 'neutralSite' => true, 'noFans' => false, 'isPlayoff' => false, 'homeQbChange' => true, 'awayQbChange' => false]
        ];

        foreach ($games as $game) {
            $elo->updateRatings(
                $game['homeTeam'], $game['awayTeam'], $game['homeScore'], $game['awayScore'],
                $game['distance'], $game['homeRested'], $game['awayRested'], $game['neutralSite'],
                $game['noFans'], $game['isPlayoff'], $game['homeQbChange'], $game['awayQbChange']
            );

            $expectedScore = $elo->getActualScorePrediction(
                $game['homeTeam'], $game['awayTeam'], $game['distance'], $game['neutralSite'],
                $game['noFans'], $game['isPlayoff']
            );

            $this->info("Expected Actual Score for {$game['homeTeam']} vs {$game['awayTeam']}: Home: {$expectedScore['teamA']}, Away: {$expectedScore['teamB']}");
        }

        $this->info('Updated Elo ratings:');
        print_r($elo->getRatings());

        // Example: Calculate the win probability for a specific matchup
        $probability = $elo->calculateWinProbability('TeamA', 'TeamB', 500, false, false, false);
        $this->info("TeamA win probability: {$probability}");
    }
}
