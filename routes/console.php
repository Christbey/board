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
Schedule::command('fetch:odds-api')->hourly(); // Ensure 'fetch:odds-api' is correctly defined

// Schedule News Command to run every fifteen minutes
Schedule::command('nfl:fetch-news --topNews')->everyFiveMinutes(); // Comment says every fifteen minutes but it's set to every five
