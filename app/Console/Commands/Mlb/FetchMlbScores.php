<?php

// app/Console/Commands/FetchMlbScoresCommand.php

namespace App\Console\Commands\Mlb;
use Illuminate\Console\Command;
use App\Jobs\FetchScoresJob;

class FetchMlbScores extends Command
{
    protected $signature = 'scores:fetch-mlb';
    protected $description = 'Fetch the latest MLB scores from the API';

    public function handle(): void
    {
        FetchScoresJob::dispatch('mlb');
        $this->info('FetchScoresJob for MLB dispatched.');
    }
}
