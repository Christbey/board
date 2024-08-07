<?php
// app/Console/Commands/FetchNbaScoresCommand.php

namespace App\Console\Commands\Nba;

use Illuminate\Console\Command;
use App\Jobs\FetchScoresJob;

class FetchNbaScores extends Command
{
    protected $signature = 'scores:fetch-nba';
    protected $description = 'Fetch the latest NBA scores from the API';

    public function handle(): void
    {
        FetchScoresJob::dispatch('nba');
        $this->info('FetchScoresJob for NBA dispatched.');
    }
}
