<?php

use App\Http\Controllers\MlbController;
use App\Http\Controllers\NbaController;
use App\Http\Controllers\NcaaController;
use App\Http\Controllers\NflController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\OddsController;
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

// Task routes
Route::resource('tasks', TaskController::class);

// NFL routes
Route::prefix('nfl')->group(function () {
    Route::get('teams', [NflController::class, 'index'])->name('nfl.teams');
    Route::get('event', [NflController::class, 'event'])->name('nfl.event');
    Route::get('teams/{team}', [NflController::class, 'show'])->name('nfl.show');
    Route::get('teams/{teamId}/next-opponents', [NflController::class, 'getNextOpponents']);
});

// NCAA routes
Route::prefix('ncaa')->group(function () {
    Route::get('event', [NcaaController::class, 'event'])->name('ncaa.event');
    Route::get('teams', [NcaaController::class, 'index'])->name('ncaa.teams');
});

// MLB routes
Route::prefix('mlb')->group(function () {
    Route::get('teams', [MlbController::class, 'index'])->name('mlb.teams');
    Route::get('event', [MlbController::class, 'event'])->name('mlb.event');
});

// NBA routes
Route::prefix('nba')->group(function () {
    Route::get('teams', [NbaController::class, 'index'])->name('nba.teams');
    Route::get('event', [NbaController::class, 'event'])->name('nba.event');
});

// Additional route for forge servers view
Route::get('/forge-servers', function () {
    return view('forge-servers');
})->name('forge-servers');

use App\Http\Controllers\DataPreparationController;

Route::get('/data-preparation', [DataPreparationController::class, 'fetchData']);
Route::get('/fetch-data', [DataPreparationController::class, 'fetchData'])->name('fetch.data');
Route::get('/match-schedules-odds', [DataPreparationController::class, 'matchSchedulesWithOdds'])->name('match.schedules.odds');
Route::get('/predictions', [DataPreparationController::class, 'makePredictions'])->name('predictions');
Route::get('/fetch-data', [DataPreparationController::class, 'fetchData'])->name('fetchData');

use App\Http\Controllers\NFLStatsController;

Route::get('/nfl/box-score/{gameID}', [NFLStatsController::class, 'fetchBoxScore']);
