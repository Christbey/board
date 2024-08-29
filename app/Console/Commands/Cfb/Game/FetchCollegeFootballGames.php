<?php

namespace App\Console\Commands\Cfb\Game;

use App\Jobs\CollegeFootball\FetchCollegeFootballGamesJob;
use Illuminate\Console\Command;

class FetchCollegeFootballGames extends Command
{
    protected $signature = 'fetch:college-football-games {year?} {seasonType=regular}';
    protected $description = 'Fetch college football games from the API and save to database';

    public function handle()
    {
        $year = $this->argument('year') ?? config('collegefootball.default_year');
        $seasonType = $this->argument('seasonType');

        // Dispatch the job without passing service instance
        FetchCollegeFootballGamesJob::dispatch($year, $seasonType);

        $this->info("Job to fetch college football games for year {$year}, season type {$seasonType} dispatched successfully.");
    }
}
