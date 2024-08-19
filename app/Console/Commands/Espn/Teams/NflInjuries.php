<?php

namespace App\Console\Commands\Espn\Teams;

use App\Jobs\FetchNflInjuriesJob;
use Illuminate\Console\Command;

class NflInjuries extends Command
{
    protected $signature = 'espn:fetch-nfl-injuries {team_id?}';
    protected $description = 'Fetch NFL injuries from the ESPN API and store them in the database';

    public function handle()
    {
        $teamId = $this->argument('team_id');

        // Dispatch the job with the provided argument
        FetchNflInjuriesJob::dispatch($teamId);

        $this->info('FetchNflInjuries job dispatched successfully.');
    }
}
