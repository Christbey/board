<?php

namespace App\Console\Commands\Cfb\Ppa;

use App\Services\CollegeFootball\CollegeFootballApiService;
use App\Traits\CollegeFootball\CollegeFootballPpaTrait;
use Illuminate\Console\Command;

class FetchCollegeFootballPpa extends Command
{
    use CollegeFootballPpaTrait;

    protected $signature = 'fetch:college-football-ppa {year?}';
    protected $description = 'Fetch college football PPA data from the API and save to database';

    protected $collegeFootballApiService;

    public function __construct(CollegeFootballApiService $collegeFootballApiService)
    {
        parent::__construct();
        $this->collegeFootballApiService = $collegeFootballApiService;
    }

    public function handle()
    {
        $year = $this->argument('year') ?? config('collegefootball.default_year');

        $url = $this->buildApiUrl($year);
        $response = $this->collegeFootballApiService->fetchFromApi($url);

        if ($response->successful()) {
            $teams = $response->json();

            foreach ($teams as $teamData) {
                $team = $this->collegeFootballApiService->findOrCreateTeam($teamData['team']);
                $this->storeTeamPpaData($team->id, $teamData);
            }

            $this->info("College football PPA data for year {$year} fetched and saved successfully.");
        } else {
            $this->error('Failed to fetch data from the API.');
        }
    }

    private function buildApiUrl($year)
    {
        return config('collegefootball.api_base_url') . "/ppa/teams?year=$year";
    }
}
