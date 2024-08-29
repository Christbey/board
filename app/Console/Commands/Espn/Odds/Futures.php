<?php
// app/Console/Commands/Espn/Odds/Futures.php

namespace App\Console\Commands\Espn\Odds;

use App\Jobs\Nfl\FetchNflFuturesJob;
use Illuminate\Console\Command;

class Futures extends Command
{
    protected $signature = 'espn:futures {season}';
    protected $description = 'Fetch NFL futures from ESPN API';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $season = $this->argument('season');

        // Dispatch the job
        FetchNflFuturesJob::dispatch($season);

        $this->info("NFL futures job for season {$season} dispatched successfully.");
    }
}
