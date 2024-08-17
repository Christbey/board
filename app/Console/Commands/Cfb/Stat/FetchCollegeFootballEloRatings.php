<?php

namespace App\Console\Commands\Cfb\Stat;

use App\Models\CollegeFootballConference;
use App\Models\CollegeFootballEloRating;
use App\Models\CollegeFootballTeam;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchCollegeFootballEloRatings extends Command
{
    protected $signature = 'fetch:college-football-elo-ratings {year?}';
    protected $description = 'Fetch college football Elo ratings from the API and save to database';

    public function handle()
    {
        $year = $this->argument('year') ?? config('collegefootball.default_year');
        $url = $this->buildApiUrl($year);

        $response = $this->fetchEloRatingsFromApi($url);

        if ($response->successful()) {
            $this->processEloRatings($response->json(), $year);
            $this->info('College football Elo ratings fetched and stored successfully.');
        } else {
            $this->error('Failed to fetch the data.');
        }
    }

    private function buildApiUrl($year)
    {
        return config('collegefootball.api_base_url') . "/ratings/elo?year=$year";
    }

    private function fetchEloRatingsFromApi($url)
    {
        return Http::withHeaders([
            'accept' => 'application/json',
            'Authorization' => 'Bearer ' . config('collegefootball.api_key'),
        ])->get($url);
    }

    private function processEloRatings(array $teams, $year)
    {
        foreach ($teams as $team) {
            $teamRecord = $this->findOrCreateTeam($team['team']);
            $conferenceRecord = $this->findOrCreateConference($team['conference'] ?? null);

            CollegeFootballEloRating::updateOrCreate(
                [
                    'year' => $year,
                    'team_id' => $teamRecord->id,
                ],
                [
                    'conference_id' => $conferenceRecord->id ?? null,
                    'elo' => $team['elo'],
                ]
            );
        }
    }

    private function findOrCreateTeam($school)
    {
        return CollegeFootballTeam::firstOrCreate(['school' => $school]);
    }

    private function findOrCreateConference($conference)
    {
        if ($conference) {
            return CollegeFootballConference::firstOrCreate(['abbreviation' => $conference]);
        }

        return null;
    }
}
