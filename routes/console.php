<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

$sports = ['mlb', 'nba', 'nfl', 'ncaa'];
$types = ['scores', 'odds'];

foreach ($sports as $sport) {
    foreach ($types as $type) {
        Artisan::command("fetch:{$sport}-{$type}", function () use ($type, $sport) {
            $this->info("Fetching {$sport} {$type}...");
            $this->call("{$type}:fetch", ['sport' => $sport]);
        })->purpose("Fetch the latest {$sport} {$type} from the API");
    }
}

// Incorrect command or typo can cause issues

// Schedule News Command to run every fifteen minutes
Schedule::command('espn:nfl-news')->everyMinute();
Schedule::command('odds:fetch nfl')->everySixHours();
Schedule::command('scores:fetch nfl')->everySixHours();
Schedule::command('fetch:espn-events 2024 2 1')->everyFiveMinutes();
// Schedule Nfl Players Command to run daily
Schedule::command('nfl:get-players')->daily();