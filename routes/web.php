<?php

use App\Http\Controllers\MlbController;
use App\Http\Controllers\NbaController;
use App\Http\Controllers\NcaaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

use App\Http\Controllers\TaskController;

Route::resource('tasks', TaskController::class);


use App\Http\Controllers\NflController;

Route::get('/nfl/teams', [NflController::class, 'index'])->name('nfl.index');

// routes/web.php

use App\Http\Controllers\OddsController;

Route::get('/odds', [OddsController::class, 'index'])->name('odds.index');
Route::get('/odds/fetch', [OddsController::class, 'fetch'])->name('odds.fetch');


// NEW API FORMAT
Route::prefix('nfl')->group(function () {
    Route::get('odds', [NflController::class, 'showOdds'])->name('nfl.odds');
    Route::get('teams', [NflController::class, 'index'])->name('nfl.teams');
});

Route::prefix('ncaa')->group(function () {
    Route::get('odds', [NcaaController::class, 'showOdds'])->name('ncaa.odds');
    Route::get('teams', [NcaaController::class, 'index'])->name('ncaa.teams');
});

Route::prefix('mlb')->group(function () {
    Route::get('odds', [MlbController::class, 'showOdds'])->name('mlb.odds');
    Route::get('teams', [MlbController::class, 'index'])->name('mlb.teams');
});

Route::prefix('nba')->group(function () {
    Route::get('odds', [NbaController::class, 'showOdds'])->name('nba.odds');
    Route::get('teams', [NbaController::class, 'index'])->name('nba.teams');
});

Route::get('/forge-servers', function () {
    return view('forge-servers');
})->name('forge-servers');

Route::get('/mlb/scores', [MLBController::class, 'showScores'])->name('mlb.scores');
