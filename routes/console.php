<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\FetchNflOdds;
use App\Jobs\FetchNcaaOdds;
use App\Jobs\FetchNbaOdds;
use App\Jobs\FetchMlbOdds;
use App\Services\NflOddsService;
use App\Services\NcaaOddsService;
use App\Services\NbaOddsService;
use App\Services\MlbOddsService;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule the jobs to run every minute
Schedule::call(function () {
    $nflOddsService = app(NflOddsService::class);
    FetchNflOdds::dispatch($nflOddsService);
})->hourly();

Schedule::call(function () {
    $ncaaOddsService = app(NcaaOddsService::class);
    FetchNcaaOdds::dispatch($ncaaOddsService);
})->hourly();

Schedule::call(function () {
    $nbaOddsService = app(NbaOddsService::class);
    FetchNbaOdds::dispatch($nbaOddsService);
})->hourly();

Schedule::command('odds:fetch-mlb')->everyMinute();
