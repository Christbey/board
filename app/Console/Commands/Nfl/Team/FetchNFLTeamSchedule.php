<?php

namespace App\Console\Commands\Nfl\Team;

use App\Jobs\FetchNFLTeamScheduleJob;
use Illuminate\Console\Command;

class FetchNFLTeamSchedule extends Command
{
    protected $signature = 'fetch:nfl-team-schedule';
    protected $description = 'Fetch NFL team schedule for the configured season';

    public function handle(): void
    {
        // Get the season from the config
        $season = config('nfl.season');

        $this->info('Fetching schedule for the hardcoded team for season: ' . $season);

        // Hardcoded team abbreviation for testing
        $teamAbv = 'KC'; // Example: 'DAL' for Dallas Cowboys

        // Dispatch the job for the hardcoded team
        FetchNFLTeamScheduleJob::dispatch($teamAbv, $season);

        $this->info("Schedule for team {$teamAbv} is being processed.");
    }
}
