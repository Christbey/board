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

// Function to register fetch commands
function registerFetchCommands($type, $sport): void
{
    Artisan::command("fetch:{$sport}-{$type}", function () use ($type, $sport) {
        $this->info("Fetching {$sport} {$type}...");
        $this->call("{$type}:fetch", ['sport' => $sport]);
    })->purpose("Fetch the latest {$sport} {$type} from the API");
}

// Function to schedule commands
function scheduleFetchCommands($type, $scheduleMethod): void
{
    $sports = ['mlb', 'nba', 'nfl', 'ncaa'];
    foreach ($sports as $sport) {
        Schedule::command("fetch:{$sport}-{$type}")->{$scheduleMethod}();
    }
}

// Register Scores Commands
registerFetchCommands('scores', 'mlb');
registerFetchCommands('scores', 'nba');
registerFetchCommands('scores', 'nfl');
registerFetchCommands('scores', 'ncaa');

// Register Odds Commands
registerFetchCommands('odds', 'mlb');
registerFetchCommands('odds', 'nba');
registerFetchCommands('odds', 'nfl');
registerFetchCommands('odds', 'ncaa');

// Schedule the Commands
scheduleFetchCommands('odds', 'twiceDaily');
scheduleFetchCommands('scores', 'hourly');

// Schedule News Command to run every fifteen minutes
Schedule::command('nfl:fetch-news --topNews')->everyFifteenMinutes();
