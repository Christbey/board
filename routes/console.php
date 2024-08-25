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


// Schedule News Command to run every fifteen minutes
Schedule::command('espn:nfl-news')->everyTenMinutes();

// Schedule Nfl Odds Command to run every thirty minutes
Schedule::command('odds:fetch nfl')->everyFiveMinutes();

// Schedule Nfl Players Command to run daily
Schedule::command('nfl:get-players')->dailyAt('23:30');

// Schedule Nfl Games Command to run daily
Schedule::command('notify:daily-games')->dailyAt('08:00');

// Schedule Nfl Injuries Command to run every five minutes
Schedule::command('nfl:fetch-injuries')->everyThirtyMinutes();

// Schedule Calculate QBR Command to run daily
Schedule::command('calculate:qbr')->dailyAt('13:00');

// Schedule Nfl Team Schedule Command to run every six hours
Schedule::command('fetch:nfl-team-schedule')->everySixHours();

// Schedule College Football Games Command to run every Saturday every 30 minutes, starting from 8 AM CST.
Schedule::command('fetch:college-football-games')
    ->saturdays()
    ->everyThirtyMinutes()
    ->timezone('America/Chicago')  // CST time zone
    ->between('08:00', '23:59');   // Run between 8:00 AM and 11:59 PM CST

// Schedule College Football FPI Ratings Command to run every Monday at 3:00 PM CST.
Schedule::command('fetch:college-football-fpi-ratings')->daily()->at('15:00');

// Schedule College Football Rankings Command to run every Monday at 4:00 PM CST.
Schedule::command('fetch:college-football-rankings')
    ->mondays()
    ->timezone('America/Chicago')
    ->at('16:00');

Schedule::command('log:predicted-scores')->daily();
Schedule::command('fetch:espn-events 2024 4 1')->daily();

// Schedule Ncaa Odds Command to run hourly
Schedule::command('fetch:ncaa-odds')->hourly();
