<?php

namespace App\Console\Commands\Cfb\Game;

use App\Jobs\FetchCollegeFootballGamesJob;
use App\Services\CollegeFootballApiService;
use Illuminate\Console\Command;

class FetchCollegeFootballGames extends Command
{
    protected $signature = 'fetch:college-football-games {year?} {seasonType=regular}';
    protected $description = 'Fetch college football games from the API and save to database';
    protected $service;

    public function __construct(CollegeFootballApiService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    public function handle()
    {
        $year = $this->argument('year') ?? config('collegefootball.default_year');
        $seasonType = $this->argument('seasonType');

        // Dispatch the job and pass the service instance
        FetchCollegeFootballGamesJob::dispatch($year, $seasonType, $this->service);

        $this->info("Job to fetch college football games for year {$year}, season type {$seasonType} dispatched successfully.");
    }
}
