<?php

namespace App\Console\Commands\Cfb\Stat;

use App\Jobs\FetchCollegeFootballRankingsJob;
use Illuminate\Console\Command;

class FetchCollegeFootballRankings extends Command
{
    protected $signature = 'fetch:college-football-rankings {year=2024} {week=1} {seasonType=regular}';
    protected $description = 'Fetch college football rankings from the API and save to database';

    public function handle()
    {
        $year = $this->argument('year');
        $week = $this->argument('week');
        $seasonType = $this->argument('seasonType');

        FetchCollegeFootballRankingsJob::dispatch($year, $week, $seasonType);

        $this->info("Job to fetch college football rankings for year {$year}, week {$week}, and season type {$seasonType} dispatched successfully.");
    }
}
