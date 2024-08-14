<?php

namespace App\Console\Commands\Cfb;

use App\Models\CollegeFootballConference;
use App\Models\CollegeFootballFpiRating;
use App\Models\CollegeFootballTeam;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchCollegeFootballFpiRatings extends Command
{
    protected $signature = 'fetch:college-football-fpi-ratings {year=2024}';
    protected $description = 'Fetch college football FPI ratings from the API and save to database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $year = $this->argument('year');

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'Authorization' => 'Bearer ' . env('COLLEGE_FOOTBALL_DATA_API_KEY'),
        ])->get("https://api.collegefootballdata.com/ratings/fpi?year={$year}");

        if ($response->successful()) {
            $teams = $response->json();

            foreach ($teams as $team) {
                // Find or create the team
                $teamRecord = CollegeFootballTeam::firstOrCreate(
                    ['school' => $team['team']],
                    ['school' => $team['team']]
                );

                // Find or create the conference
                $conferenceRecord = null;
                if (isset($team['conference'])) {
                    $conferenceRecord = CollegeFootballConference::firstOrCreate(
                        ['abbreviation' => $team['conference']],
                        ['abbreviation' => $team['conference']]
                    );
                }

                // Store the FPI rating data
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

            $this->info("College football FPI ratings for year {$year} fetched and saved successfully.");
        } else {
            $this->error('Failed to fetch data from the API.');
        }
    }
}
