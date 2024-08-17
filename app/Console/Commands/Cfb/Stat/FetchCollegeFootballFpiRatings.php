<?php

namespace App\Console\Commands\Cfb\Stat;

use App\Models\CollegeFootballConference;
use App\Models\CollegeFootballFpiRating;
use App\Models\CollegeFootballTeam;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchCollegeFootballFpiRatings extends Command
{
    protected $signature = 'fetch:college-football-fpi-ratings {year?}';
    protected $description = 'Fetch college football FPI ratings from the API and save to database';

    public function handle()
    {
        $year = $this->argument('year') ?? config('collegefootball.default_year');
        $url = $this->buildApiUrl($year);

        $response = $this->fetchFpiRatingsFromApi($url);

        if ($response->successful()) {
            $this->processFpiRatings($response->json(), $year);
            $this->info('College football FPI ratings fetched and stored successfully.');
        } else {
            $this->error('Failed to fetch the data.');
        }
    }

    private function buildApiUrl($year)
    {
        return config('collegefootball.api_base_url') . "/ratings/fpi?year=$year";
    }

    private function fetchFpiRatingsFromApi($url)
    {
        return Http::withHeaders([
            'accept' => 'application/json',
            'Authorization' => 'Bearer ' . config('collegefootball.api_key'),
        ])->get($url);
    }

    private function processFpiRatings(array $teams, $year)
    {
        foreach ($teams as $team) {
            $teamRecord = $this->findOrCreateTeam($team['team']);
            $conferenceRecord = $this->findOrCreateConference($team['conference'] ?? null);

            CollegeFootballFpiRating::updateOrCreate(
                [
                    'year' => $team['year'],
                    'team_id' => $teamRecord->id,
                ],
                [
                    'conference_id' => $conferenceRecord->id ?? null,
                    'fpi' => $team['fpi'] ?? null,
                    'strength_of_record' => $team['resumeRanks']['strengthOfRecord'] ?? null,
                    'resume_fpi' => $team['resumeRanks']['fpi'] ?? null,
                    'average_win_probability' => $team['resumeRanks']['averageWinProbability'] ?? null,
                    'strength_of_schedule' => $team['resumeRanks']['strengthOfSchedule'] ?? null,
                    'remaining_strength_of_schedule' => $team['resumeRanks']['remainingStrengthOfSchedule'] ?? null,
                    'game_control' => $team['resumeRanks']['gameControl'] ?? null,
                    'efficiency_overall' => $team['efficiencies']['overall'] ?? null,
                    'efficiency_offense' => $team['efficiencies']['offense'] ?? null,
                    'efficiency_defense' => $team['efficiencies']['defense'] ?? null,
                    'efficiency_special_teams' => $team['efficiencies']['specialTeams'] ?? null,
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
