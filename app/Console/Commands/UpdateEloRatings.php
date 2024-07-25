<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NflPredictionService;

class UpdateEloRatings extends Command
{
    protected $signature = 'elo:update';
    protected $description = 'Update Elo ratings and calculate EPA based on the latest NFL game results';

    protected NflPredictionService $nflPredictionService;

    public function __construct(NflPredictionService $nflPredictionService)
    {
        parent::__construct();
        $this->nflPredictionService = $nflPredictionService;
    }

    public function handle()
    {
        $this->info('Starting Elo ratings update...');

        // Log expected winning percentages and predicted scores
        $this->info('Logging expected winning percentages and predicted scores for future games...');
        $result = $this->nflPredictionService->logPredictedScores();
        $this->info($result);

        // Get updated Elo ratings
        $this->info('Displaying updated Elo ratings...');
        $this->displayUpdatedRatings();

        $this->info('Elo ratings update completed.');
    }

    private function displayUpdatedRatings()
    {
        $ratings = $this->nflPredictionService->eloRatingSystem->getRatings();

        $this->info('Updated Elo ratings:');
        foreach ($ratings as $team => $rating) {
            $this->line("Team ID {$team}: {$rating}");
        }
    }
}
