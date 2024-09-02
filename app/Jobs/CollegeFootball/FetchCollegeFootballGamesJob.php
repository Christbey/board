<?php

namespace App\Jobs\CollegeFootball;

use App\Models\CollegeFootballGame;
use App\Services\CollegeFootball\CollegeFootballApiService;
use App\Traits\UpdatesCollegeFootballGames;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FetchCollegeFootballGamesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, UpdatesCollegeFootballGames;

    protected $year;
    protected $seasonType;

    public function __construct($year, $seasonType)
    {
        $this->year = $year;
        $this->seasonType = $seasonType;
    }

    public function handle(CollegeFootballApiService $service)
    {
        $apiBaseUrl = config('collegefootball.api_base_url');
        $url = "{$apiBaseUrl}/games?year={$this->year}&seasonType={$this->seasonType}";

        $response = $service->fetchFromApi($url);

        if ($response->successful()) {
            $games = $response->json();

            Log::info('Fetched games from API', ['games' => $games]);

            if ($this->year == Carbon::now()->year) {
                // Filter games within 72 hours from now only if the year is the current year
                $games = $this->filterGamesByDate($games);
            }

            $this->processGames($games, $service);
        } else {
            Log::error('Failed to fetch data from the API.');
        }
    }

    protected function filterGamesByDate(array $games): array
    {
        // Start the filtering window 24 hours before now
        $start = Carbon::now()->subHours(200);
        // End the filtering window 72 hours after now
        $end = Carbon::now()->addHours(72);

        return array_filter($games, function ($game) use ($start, $end) {
            $gameStartDate = Carbon::parse($game['start_date']);
            // Include games that started between 24 hours ago and 72 hours from now
            return $gameStartDate->between($start, $end);
        });
    }

    protected function processGames(array $games, CollegeFootballApiService $service)
    {
        $teams = [];
        $conferences = [];

        foreach ($games as $game) {
            $homeTeam = $this->findOrCacheTeam($game['home_team'], $service, $teams);
            $awayTeam = $this->findOrCacheTeam($game['away_team'], $service, $teams);
            $homeConference = $this->findOrCacheConference($game['home_conference'], $service, $conferences);
            $awayConference = $this->findOrCacheConference($game['away_conference'], $service, $conferences);

            // Skip processing if any required ID is null
            if (!$homeTeam || !$awayTeam || !$homeTeam->id || !$awayTeam->id) {
                Log::warning('Skipping game due to missing team or conference IDs', [
                    'game_id' => $game['id'],
                    'home_team' => $homeTeam ? $homeTeam->name : null,
                    'away_team' => $awayTeam ? $awayTeam->name : null,
                    'home_team_id' => $homeTeam ? $homeTeam->id : null,
                    'away_team_id' => $awayTeam ? $awayTeam->id : null,
                    'home_conference_id' => $homeConference ? $homeConference->id : null,
                    'away_conference_id' => $awayConference ? $awayConference->id : null,
                ]);
                continue; // Skip this game and move to the next one
            }

            $updatedGame = $this->updateOrCreateCollegeFootballGame($game, $homeTeam, $awayTeam, $homeConference, $awayConference);

            if ($updatedGame) {
                $this->checkAndLogScoreUpdates($updatedGame);
            } else {
                Log::error('Failed to save game', ['game_id' => $game['id']]);
            }
        }
    }

    protected function findOrCacheTeam($teamId, CollegeFootballApiService $service, &$teams)
    {
        if (!isset($teams[$teamId])) {
            $teams[$teamId] = $service->findTeam($teamId);
        }

        return $teams[$teamId];
    }

    protected function findOrCacheConference($conferenceId, CollegeFootballApiService $service, &$conferences)
    {
        if (!isset($conferences[$conferenceId])) {
            $conferences[$conferenceId] = $service->findConference($conferenceId);
        }

        return $conferences[$conferenceId];
    }

    protected function updateOrCreateCollegeFootballGame($game, $homeTeam, $awayTeam, $homeConference, $awayConference)
    {
        try {
            Log::info('Attempting to update or create game', [
                'game_id' => $game['id'],
                'home_team' => $homeTeam->name,
                'away_team' => $awayTeam->name,
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
                'home_conference_id' => $homeConference ? $homeConference->id : null,
                'away_conference_id' => $awayConference ? $awayConference->id : null,
            ]);

            $updatedGame = CollegeFootballGame::updateOrCreate(
                ['id' => $game['id']],
                [
                    'season' => $game['season'],
                    'week' => $game['week'],
                    'season_type' => $game['season_type'],
                    'start_date' => $game['start_date'],
                    'start_time_tbd' => $game['start_time_tbd'] ?? false,
                    'completed' => $game['completed'] ?? false,
                    'neutral_site' => $game['neutral_site'] ?? false,
                    'conference_game' => $game['conference_game'] ?? false,
                    'attendance' => $game['attendance'] ?? null,
                    'venue_id' => $game['venue_id'] ?? null,
                    'venue' => $game['venue'] ?? null,
                    'home_id' => $game['home_id'],
                    'home_team' => $game['home_team'],
                    'home_conference' => $game['home_conference'],
                    'home_division' => $game['home_division'] ?? null,
                    'home_points' => $game['home_points'] ?? null,
                    'home_line_scores' => $game['home_line_scores'],
                    'home_pregame_elo' => $game['home_pregame_elo'] ?? null,
                    'home_postgame_elo' => $game['home_postgame_elo'] ?? null,
                    'away_id' => $game['away_id'],
                    'away_team' => $game['away_team'],
                    'away_conference' => $game['away_conference'],
                    'away_division' => $game['away_division'] ?? null,
                    'away_points' => $game['away_points'] ?? null,
                    'away_line_scores' => $game['away_line_scores'],
                    'home_post_win_prob' => $game['home_post_win_prob'] ?? null,
                    'away_post_win_prob' => $game['away_post_win_prob'] ?? null,
                    'away_pregame_elo' => $game['away_pregame_elo'] ?? null,
                    'away_postgame_elo' => $game['away_postgame_elo'] ?? null,
                    'excitement_index' => $game['excitement_index'] ?? null,
                    'highlights' => $game['highlights'] ?? null,
                    'notes' => $game['notes'] ?? null,
                    'home_team_id' => $homeTeam->id,
                    'away_team_id' => $awayTeam->id,
                    'home_conference_id' => $homeConference ? $homeConference->id : null,
                    'away_conference_id' => $awayConference ? $awayConference->id : null,
                ]
            );

            Log::info('Game saved successfully', ['game' => $updatedGame->toArray()]);

            return $updatedGame;
        } catch (Exception $e) {
            Log::error('Failed to update or create game', [
                'game_id' => $game['id'],
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }


    protected function checkAndLogScoreUpdates($updatedGame)
    {
        Log::info('Checking for score updates', [
            'game_id' => $updatedGame->id,
            'home_team' => $updatedGame->home_team,
            'away_team' => $updatedGame->away_team,
        ]);

        if ($updatedGame->wasChanged('home_line_scores')) {
            Log::info('Update!', [
                'game_id' => $updatedGame->id,
                'home_team' => $updatedGame->home_team,
                'away_team' => $updatedGame->away_team,
                'updated_scores' => json_encode($updatedGame->home_line_scores),
            ]);
        }
    }
}
