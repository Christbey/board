<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FetchOddsApi extends Command
{
    protected $signature = 'fetch:odds-api';
    protected $description = 'Fetch all sports data and odds';
    public function handle(): void
    {
        $this->call('fetch:mlb-scores');
        $this->call('fetch:nba-scores');
        $this->call('fetch:nfl-scores');
        $this->call('fetch:ncaa-scores');
        $this->call('fetch:mlb-odds');
        $this->call('fetch:nba-odds');
        $this->call('fetch:nfl-odds');
        $this->call('fetch:ncaa-odds');

        $this->info('Odds Api Updated successfully.');
    }
}
