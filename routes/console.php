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

// Schedule FetchOddsCommand for different sports
Schedule::command('odds:fetch nfl')->everySixHours();
Schedule::command('odds:fetch ncaa')->everySixHours();
Schedule::command('odds:fetch nba')->everySixHours();
Schedule::command('odds:fetch mlb')->everySixHours();


Schedule::command('scores:fetch nfl')->everySixHours();
Schedule::command('scores:fetch ncaa')->everySixHours();
Schedule::command('scores:fetch nba')->everySixHours();
Schedule::command('scores:fetch mlb')->everySixHours();

