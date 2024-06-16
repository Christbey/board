<?php

// app/Console/Commands/FetchNflScores.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\FetchScoresJob;

class FetchNflScores extends Command
{
    protected $signature = 'fetch:nfl-scores';
    protected $description = 'Fetch the latest NFL scores from the API';

    public function handle(): void
    {
        FetchScoresJob::dispatch('nfl');
        $this->info('FetchScoresJob for NFL dispatched.');
    }
}
