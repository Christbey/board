<?php

namespace App\Console\Commands;

use App\Services\NflPredictionService;
use Illuminate\Console\Command;

class LogPredictedScores extends Command
{
    protected $signature = 'log:predicted-scores';
    protected $description = 'Log expected winning percentage and predicted scores for NFL games';

    private NflPredictionService $nflService;

    public function __construct(NflPredictionService $nflService)
    {
        parent::__construct();
        $this->nflService = $nflService;
    }

    public function handle()
    {
        $result = $this->nflService->logPredictedScores();
        $this->info($result);
    }
}
