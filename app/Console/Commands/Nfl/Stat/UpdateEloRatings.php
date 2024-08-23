<?php

namespace App\Console\Commands\Nfl\Stat;

use App\Services\NflPredictionService;
use Illuminate\Console\Command;

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

        // Get updated Elo ratings
        $this->info('Displaying updated Elo ratings...');

        $this->info('Elo ratings update completed.');
    }


}
