<?php

namespace App\Console\Commands\Cfb\Stat;

use App\Jobs\CollegeFootball\FetchCollegeFootballFpiRatingsJob;
use Illuminate\Console\Command;

class FetchCollegeFootballFpiRatings extends Command
{
    protected $signature = 'fetch:college-football-fpi-ratings {year?}';
    protected $description = 'Fetch college football FPI ratings from the API and save to database';

    public function handle()
    {
        $year = $this->argument('year') ?? config('collegefootball.default_year');

        FetchCollegeFootballFpiRatingsJob::dispatch($year);

        $this->info("Job to fetch college football FPI ratings for year {$year} dispatched successfully.");
    }
}
