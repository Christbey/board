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

use App\Http\Controllers\EspnController;

Route::get('/espn/team/{team_id}/schedule', [EspnController::class, 'showNflSchedule'])->name('espn.schedule');
Route::get('/espn/team/{team_id}/details', [App\Http\Controllers\EspnController::class, 'showTeamDetails'])->name('espn.team-details');
Route::post('/espn/team/details/filter', [App\Http\Controllers\EspnController::class, 'filterTeam'])->name('filter_team');
Route::get('/espn-nfl-odds', [EspnController::class, 'showNflOdds']);
Route::get('/espn-nfl-scoreboard', [EspnController::class, 'showNflScoreboard']);
Route::get('/espn-nfl-team-projection', [EspnController::class, 'showNflTeamProjection']);

use App\Http\Controllers\DynamicNFLController;


Route::get('/nfl/fetch', [DynamicNFLController::class, 'fetch']);

// routes/web.php
Route::get('/espn/events', [App\Http\Controllers\EspnEventController::class, 'index'])->name('espn.events');
Route::post('/espn/events/filter', [App\Http\Controllers\EspnEventController::class, 'filter'])->name('espn.events.filter');
Route::get('/espn/depth-chart', [EspnController::class, 'showDepthChart'])->name('espn.depth-chart');
Route::get('/espn/injuries', [EspnController::class, 'showInjuries'])->name('espn.injuries');
Route::get('/espn/nfl/teams', [EspnController::class, 'index'])->name('espn.nfl.teams.index');
Route::get('/espn/nfl/teams/{id}', [EspnController::class, 'show'])->name('espn.nfl.teams.show');
Route::get('/espn/nfl/events/{event_id}', [EspnController::class, 'showEvent'])->name('espn.events.show');
