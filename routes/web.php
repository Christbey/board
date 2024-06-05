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
    Route::get('odds', [NflController::class, 'showOdds'])->name('nfl.odds');
    Route::get('teams', [NflController::class, 'index'])->name('nfl.teams');
    Route::get('scores', [NflController::class, 'showScores'])->name('nfl.scores');
});

// NCAA routes
Route::prefix('ncaa')->group(function () {
    Route::get('odds', [NcaaController::class, 'showOdds'])->name('ncaa.odds');
    Route::get('teams', [NcaaController::class, 'index'])->name('ncaa.teams');
});

// MLB routes
Route::prefix('mlb')->group(function () {
    Route::get('odds', [MlbController::class, 'showOdds'])->name('mlb.odds');
    Route::get('teams', [MlbController::class, 'index'])->name('mlb.teams');
    Route::get('scores', [MlbController::class, 'showScores'])->name('mlb.scores');
});

// NBA routes
Route::prefix('nba')->group(function () {
    Route::get('odds', [NbaController::class, 'showOdds'])->name('nba.odds');
    Route::get('teams', [NbaController::class, 'index'])->name('nba.teams');
    Route::get('scores', [NbaController::class, 'showScores'])->name('nba.scores');
});


// Additional route for forge servers view
Route::get('/forge-servers', function () {
    return view('forge-servers');
})->name('forge-servers');


