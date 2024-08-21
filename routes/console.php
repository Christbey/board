<?php

use App\Jobs\SendDailyGameScheduleNotification;
use App\Models\NflTeam;
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
Schedule::command('odds:fetch nfl')->everyThirtyMinutes();
Schedule::command('fetch:espn-events 2024 3 1')->daily();
// Schedule Nfl Players Command to run daily
Schedule::command('nfl:get-players')->daily();
Schedule::command('notify:daily-games')->dailyAt('08:00');
Schedule::command('espn:fetch-nfl-injuries')->hourly();
Schedule::command('calulate:qbr')->daily();
Schedule::command('log:predicted-scores')->daily();
Schedule::command('fetch:nfl-team-schedule')->everySixHours();