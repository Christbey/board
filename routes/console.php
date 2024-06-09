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

// Register Commands

Artisan::command('scores:fetch-mlb', function () {
    $this->info('Fetching MLB scores...');
    $this->call('scores:fetch', ['sport' => 'mlb']);
})->purpose('Fetch the latest MLB scores from the API');