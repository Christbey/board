<?php

namespace App\Jobs\CollegeFootball;

use App\Models\CollegeFootballConference;
use App\Models\CollegeFootballFpiRating;
use App\Models\CollegeFootballTeam;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchCollegeFootballFpiRatingsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $year;

    protected $teams = [];
    protected $conferences = [];

    public function __construct($year)
    {
        $this->year = $year;
    }

    public function handle()
    {
        $year = $this->year;
        $url = $this->buildApiUrl($year);

        $response = $this->fetchFpiRatingsFromApi($url);

        if ($response->successful()) {
            $this->cacheTeamsAndConferences();
            $this->processFpiRatings($response->json(), $year);
            Log::info('College football FPI ratings fetched and stored successfully.');
        } else {
            Log::error('Failed to fetch the data.');
            Log::error("Failed to fetch FPI ratings for year $year. Response: " . $response->body());
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
        $fpiData = [];

        foreach ($teams as $team) {
            $teamId = $this->getTeamId($team['team']);
            $conferenceId = $this->getConferenceId($team['conference'] ?? null);

            $fpiData[] = [
                'year' => $team['year'],
                'team_id' => $teamId,
                'conference_id' => $conferenceId,
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
            ];
        }

        CollegeFootballFpiRating::upsert(
            $fpiData,
            ['year', 'team_id'],
            [
                'conference_id', 'fpi', 'strength_of_record', 'resume_fpi',
                'average_win_probability', 'strength_of_schedule',
                'remaining_strength_of_schedule', 'game_control',
                'efficiency_overall', 'efficiency_offense',
                'efficiency_defense', 'efficiency_special_teams'
            ]
        );
    }

    private function cacheTeamsAndConferences()
    {
        $this->teams = CollegeFootballTeam::pluck('id', 'school')->all();
        $this->conferences = CollegeFootballConference::pluck('id', 'abbreviation')->all();
    }

    private function getTeamId($school)
    {
        return $this->teams[$school] ?? $this->teams[$school] = CollegeFootballTeam::firstOrCreate(['school' => $school])->id;
    }

    private function getConferenceId($conference)
    {
        if ($conference) {
            return $this->conferences[$conference] ?? $this->conferences[$conference] = CollegeFootballConference::firstOrCreate(['abbreviation' => $conference])->id;
        }

        return null;
    }
}
