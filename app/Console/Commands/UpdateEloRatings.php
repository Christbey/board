<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EloRatingSystem;

class UpdateEloRatings extends Command
{
    protected $signature = 'elo:update';
    protected $description = 'Update Elo ratings for NFL teams based on recent game results';

    private $teams = ['TeamA', 'TeamB', 'TeamC', 'TeamD']; // Add your team names here

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $elo = new EloRatingSystem($this->teams);

        // Simulate some games. In a real application, you would fetch these results from your database or an API.
        $games = [
            ['homeTeam' => 'TeamA', 'awayTeam' => 'TeamB', 'homeScore' => 24, 'awayScore' => 17, 'distance' => 100, 'homeRested' => false, 'awayRested' => false, 'neutralSite' => false, 'noFans' => false, 'isPlayoff' => false, 'homeQbChange' => false, 'awayQbChange' => false],
            ['homeTeam' => 'TeamC', 'awayTeam' => 'TeamD', 'homeScore' => 21, 'awayScore' => 21, 'distance' => 2000, 'homeRested' => true, 'awayRested' => false, 'neutralSite' => false, 'noFans' => true, 'isPlayoff' => true, 'homeQbChange' => false, 'awayQbChange' => false],
            ['homeTeam' => 'TeamA', 'awayTeam' => 'TeamC', 'homeScore' => 14, 'awayScore' => 28, 'distance' => 1500, 'homeRested' => false, 'awayRested' => true, 'neutralSite' => true, 'noFans' => false, 'isPlayoff' => false, 'homeQbChange' => true, 'awayQbChange' => false]
        ];

        foreach ($games as $game) {
            $elo->updateRatings(
                $game['homeTeam'], $game['awayTeam'], $game['homeScore'], $game['awayScore'],
                $game['distance'], $game['homeRested'], $game['awayRested'], $game['neutralSite'],
                $game['noFans'], $game['isPlayoff'], $game['homeQbChange'], $game['awayQbChange']
            );
        }

        $this->info('Updated Elo ratings:');
        print_r($elo->getRatings());

        // Example: Calculate the win probability for a specific matchup
        $probability = $elo->calculateWinProbability('TeamA', 'TeamB', 500, false, false, false);
        $this->info("TeamA win probability: {$probability}");
    }
}
