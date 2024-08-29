<?php

namespace App\Console\Commands\Cfb\Ppa;

use App\Services\CollegeFootball\CollegeFootballApiService;
use App\Traits\CollegeFootball\CollegeFootballPpaTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FetchCollegeFootballGamePpa extends Command
{
    use CollegeFootballPpaTrait;

    protected $signature = 'fetch:college-football-game-ppa {year?} {seasonType?}';
    protected $description = 'Fetch college football game PPA data from the API and save to database';

    protected $collegeFootballApiService;

    public function __construct(CollegeFootballApiService $collegeFootballApiService)
    {
        parent::__construct();
        $this->collegeFootballApiService = $collegeFootballApiService;
    }

    public function handle()
    {
        $year = $this->argument('year') ?? config('collegefootball.default_year');
        $seasonType = $this->argument('seasonType') ?? 'regular';

        $url = $this->buildApiUrl($year, $seasonType);
        $response = $this->collegeFootballApiService->fetchFromApi($url);

        if ($response->successful()) {
            $games = $response->json();

            foreach ($games as $game) {
                $team = $this->collegeFootballApiService->findTeam($game['team']);
                $opponent = $this->collegeFootballApiService->findTeam($game['opponent']);
                $conference = $this->collegeFootballApiService->findConference($game['conference']);
                $collegeGame = $this->collegeFootballApiService->findCollegeGame($game['gameId']);

                if (!$team || !$opponent || !$conference || !$collegeGame) {
                    Log::error("Missing data for game ID {$game['gameId']}: Team, Opponent, Conference, or College Game not found.");
                    continue;
                }

                $this->storeGamePpaData($collegeGame->id, $team->id, $opponent->id, $conference->id, $game);
            }

            $this->info("College football game PPA data for year {$year}, season type {$seasonType} fetched and saved successfully.");
        } else {
            $this->error('Failed to fetch data from the API.');
        }
    }

    private function buildApiUrl($year, $seasonType)
    {
        return config('collegefootball.api_base_url') . "/ppa/games?year=$year&seasonType=$seasonType";
    }
}
