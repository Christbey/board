<?php

use App\Http\Controllers\CollegeFootballDataController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/games', [CollegeFootballDataController::class, 'getGames']);
Route::get('/calendar', [CollegeFootballDataController::class, 'getCalendar']);
Route::get('/games/media', [CollegeFootballDataController::class, 'getGameMedia']);
Route::get('/games/weather', [CollegeFootballDataController::class, 'getGameWeather']);
Route::get('/games/players', [CollegeFootballDataController::class, 'getPlayerGameStats']);
Route::get('/games/teams', [CollegeFootballDataController::class, 'getTeamGameStats']);
Route::get('/game/box/advanced', [CollegeFootballDataController::class, 'getAdvancedBoxScore']);
Route::get('/drives', [CollegeFootballDataController::class, 'getDrives']);
Route::get('/plays', [CollegeFootballDataController::class, 'getPlays']);
Route::get('/live/plays', [CollegeFootballDataController::class, 'getLivePlays']);
Route::get('/play/types', [CollegeFootballDataController::class, 'getPlayTypes']);
Route::get('/play/stats', [CollegeFootballDataController::class, 'getPlayStats']);
Route::get('/conferences', [CollegeFootballDataController::class, 'getConferences']);
Route::get('/teams', [CollegeFootballDataController::class, 'getTeams']);
Route::get('/teams/fbs', [CollegeFootballDataController::class, 'getFbsTeams']);
Route::get('/roster', [CollegeFootballDataController::class, 'getRoster']);

