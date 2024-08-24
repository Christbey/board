<?php

namespace App\Jobs;

use App\Services\CollegeFootballApiService;
use App\Traits\UpdatesCollegeFootballGames;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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

            // Filter games within 48 hours from now
            $filteredGames = array_filter($games, function ($game) {
                $startDate = Carbon::parse($game['start_date']);
                $now = Carbon::now();
                return $startDate->between($now, $now->addHours(48));
            });

            $filteredGames = $this->filterGamesByDate($games);

            $this->processGames($filteredGames, $service);
        } else {
            Log::error('Failed to fetch data from the API.');
        }
        
    }

    protected function filterGamesByDate(array $games): array
    {
        $now = Carbon::now();
        $end = $now->copy()->addHours(48);

        return array_filter($games, function ($game) use ($now, $end) {
            $gameStartDate = Carbon::parse($game['start_date']);
            return $gameStartDate->between($now, $end);
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

            $updatedGame = $this->updateOrCreateCollegeFootballGame($game, $homeTeam, $awayTeam, $homeConference, $awayConference);

            $this->checkAndLogScoreUpdates($updatedGame);
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

    protected function checkAndLogScoreUpdates($updatedGame)
    {
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
