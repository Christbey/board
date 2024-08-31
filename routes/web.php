<?php

use App\Http\Controllers\CollegeFootballController;
use App\Http\Controllers\CollegeFootballPredictionController;
use App\Http\Controllers\DataPreparationController;
use App\Http\Controllers\DynamicNFLController;
use App\Http\Controllers\EspnController;
use App\Http\Controllers\EspnEventController;
use App\Http\Controllers\MlbController;
use App\Http\Controllers\NbaController;
use App\Http\Controllers\NcaaController;
use App\Http\Controllers\NflController;
use App\Http\Controllers\NflOddsController;
use App\Http\Controllers\NFLStatsController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

// Public route
Route::get('/', function () {
    return view('welcome');
});

// Routes that require authentication
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {


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

    Route::get('/data-preparation', [DataPreparationController::class, 'fetchData']);
    Route::get('/fetch-data', [DataPreparationController::class, 'fetchData'])->name('fetch.data');
    Route::get('/match-schedules-odds', [DataPreparationController::class, 'matchSchedulesWithOdds'])->name('match.schedules.odds');
    Route::get('/predictions', [DataPreparationController::class, 'makePredictions'])->name('predictions');
    Route::get('/fetch-data', [DataPreparationController::class, 'fetchData'])->name('fetchData');

    Route::get('/nfl/box-score/{gameID}', [NFLStatsController::class, 'fetchBoxScore']);

    Route::get('/espn/team/{team_id}/schedule', [EspnController::class, 'showNflSchedule'])->name('espn.schedule');
    Route::get('/espn/team/{team_id}/details', [EspnController::class, 'showTeamDetails'])->name('espn.team-details');
    Route::post('/espn/team/details/filter', [EspnController::class, 'filterTeam'])->name('filter_team');
    Route::get('/espn-nfl-odds', [EspnController::class, 'showNflOdds']);
    Route::get('/espn-nfl-scoreboard', [EspnController::class, 'showNflScoreboard']);
    Route::get('/espn-nfl-team-projection', [EspnController::class, 'showNflTeamProjection']);

    Route::get('/nfl/fetch', [DynamicNFLController::class, 'fetch']);

    Route::get('/espn/events', [EspnEventController::class, 'index'])->name('espn.events');
    Route::post('/espn/events/filter', [EspnEventController::class, 'filter'])->name('espn.events.filter');
    Route::get('/espn/depth-chart', [EspnController::class, 'showDepthChart'])->name('espn.depth-chart');
    Route::get('/espn/injuries', [EspnController::class, 'showInjuries'])->name('espn.injuries');
    Route::get('/espn/nfl/teams', [EspnController::class, 'index'])->name('espn.nfl.teams.index');
    Route::get('/espn/nfl/teams/{id}', [EspnController::class, 'show'])->name('espn.nfl.teams.show');
    Route::get('/espn/nfl/events/{event_id}', [EspnController::class, 'showEvent'])->name('espn.events.show');

    // College Football routes
    Route::get('/college-football', [CollegeFootballController::class, 'index'])->name('cfb.teams.index');
    Route::get('/college-football/{team}', [CollegeFootballController::class, 'show'])->name('cfb.teams.show');
    Route::get('/college-football/events/index', [CollegeFootballController::class, 'event'])->name('cfb.events.index');
    Route::get('/college-football/events/{id}', [CollegeFootballController::class, 'showEvent'])->name('cfb.events.show');
    Route::get('college-football/rankings/index', [CollegeFootballController::class, 'rankings'])->name('cfb.rankings.index');

    Route::get('/nfl-odds/{eventId?}', [NflOddsController::class, 'showOdds']);

    // College Football Prediction routes
    Route::get('/predict/game', [CollegeFootballPredictionController::class, 'index'])->name('predict.index');
    Route::get('predict/game/{gameId}', [CollegeFootballPredictionController::class, 'show'])->name('predict.game');

    Route::get('/nfl/picks/{week_id}', [EspnEventController::class, 'showWeekEvents'])->name('nfl.picks.week');
    Route::post('/nfl/pick-winner', [EspnEventController::class, 'pickWinner'])->name('nfl.pickWinner');
    Route::get('/espn/picks/submissions/{weekId}', [EspnEventController::class, 'showSubmissions'])->name('espn.picks.submissions');


    // Test route
    Route::get('/test', function () {
        Log::info('Test route was called.');
        return 'Test route is working';
    });

    // Email route
    Route::get('/send-email', function () {
        Mail::raw('This is a test email.', function ($message) {
            $message->to('jcbeypeterson@icloud.com')
                ->subject('Test Email');
        });

        return 'Email sent';
    });

});
