<?php

// app/Console/Commands/FetchNcaaScores.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\FetchScoresJob;

class FetchNcaaScores extends Command
{
    protected $signature = 'fetch:ncaa-scores';
    protected $description = 'Fetch the latest NCAA scores from the API';

    public function handle()
    {
        FetchScoresJob::dispatch('ncaa');
        $this->info('FetchScoresJob for NCAA dispatched.');
    }
}
