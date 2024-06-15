<?php

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

Artisan::command('fetch:mlb-scores', function () {
    $this->info('Fetching MLB scores...');
    $this->call('scores:fetch', ['sport' => 'mlb']);
})->purpose('Fetch the latest MLB scores from the API');

Artisan::command('fetch:nba-scores', function () {
    $this->info('Fetching NBA scores...');
    $this->call('scores:fetch', ['sport' => 'nba']);
})->purpose('Fetch the latest NBA scores from the API');

Artisan::command('fetch:nfl-scores', function () {
    $this->info('Fetching NFL scores...');
    $this->call('scores:fetch', ['sport' => 'nfl']);
})->purpose('Fetch the latest NFL scores from the API');

Artisan::command('fetch:ncaa-scores', function () {
    $this->info('Fetching NCAA scores...');
    $this->call('scores:fetch', ['sport' => 'ncaa']);
})->purpose('Fetch the latest NCAA scores from the API');

// Register Odds Commands

Artisan::command('fetch:mlb-odds', function () {
    $this->info('Fetching MLB odds...');
    $this->call('odds:fetch', ['sport' => 'mlb']);
})->purpose('Fetch the latest MLB odds from the API');

Artisan::command('fetch:nba-odds', function () {
    $this->info('Fetching NBA odds...');
    $this->call('odds:fetch', ['sport' => 'nba']);
})->purpose('Fetch the latest NBA odds from the API');

Artisan::command('fetch:nfl-odds', function () {
    $this->info('Fetching NFL odds...');
    $this->call('odds:fetch', ['sport' => 'nfl']);
})->purpose('Fetch the latest NFL odds from the API');

Artisan::command('fetch:ncaa-odds', function () {
    $this->info('Fetching NCAA odds...');
    $this->call('odds:fetch', ['sport' => 'ncaa']);
})->purpose('Fetch the latest NCAA odds from the API');

// Schedule The Commands

// Schedule Commands to run twice a day
Schedule::command('fetch:mlb-odds')->twiceDaily();
Schedule::command('fetch:nba-odds')->twiceDaily();
Schedule::command('fetch:nfl-odds')->twiceDaily();
Schedule::command('fetch:ncaa-odds')->twiceDaily();

// Schedule Scores Commands to run hourly
Schedule::command('fetch:mlb-scores')->hourly();
Schedule::command('fetch:nba-scores')->hourly();
Schedule::command('fetch:nfl-scores')->hourly();
Schedule::command('fetch:ncaa-scores')->hourly();

// Schedule News Command to run hourly

Schedule::command('nfl:fetch-news')->hourly();
