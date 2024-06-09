<?php

use App\Console\Commands\FetchMlbScoresCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

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

// Register Scores Commands

Artisan::command('scores:fetch-mlb', function () {
    $this->info('Fetching MLB scores...');
    $this->call('scores:fetch', ['sport' => 'mlb']);
})->purpose('Fetch the latest MLB scores from the API');

Artisan::command('scores:fetch-nba', function () {
    $this->info('Fetching NBA scores...');
    $this->call('scores:fetch', ['sport' => 'nba']);
})->purpose('Fetch the latest NBA scores from the API');

Artisan::command('fetch:nfl-scores', function () {
    $this->info('Fetching NFL scores...');
    $this->call('fetch:scores', ['sport' => 'nfl']);
})->purpose('Fetch the latest NFL scores from the API');

Artisan::command('fetch:ncaa-scores', function () {
    $this->info('Fetching NCAA scores...');
    $this->call('fetch:scores', ['sport' => 'ncaa']);
})->purpose('Fetch the latest NCAA scores from the API');

// Register Odds Commands

Artisan::command('fetch:mlb-odds', function () {
    $this->info('Fetching MLB odds...');
    $this->call('fetch:odds', ['sport' => 'mlb']);
})->purpose('Fetch the latest MLB odds from the API');

Artisan::command('fetch:nba-odds', function () {
    $this->info('Fetching NBA odds...');
    $this->call('fetch:odds', ['sport' => 'nba']);
})->purpose('Fetch the latest NBA odds from the API');

Artisan::command('fetch:nfl-odds', function () {
    $this->info('Fetching NFL odds...');
    $this->call('fetch:odds', ['sport' => 'nfl']);
})->purpose('Fetch the latest NFL odds from the API');

Artisan::command('fetch:ncaa-odds', function () {
    $this->info('Fetching NCAA odds...');
    $this->call('fetch:odds', ['sport' => 'ncaa']);
})->purpose('Fetch the latest NCAA odds from the API');

// Register FetchOddsCommand Commands

