<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CollegeFootballEloRating;
use App\Models\CollegeFootballTeam;
use App\Models\CollegeFootballConference;
use Illuminate\Support\Facades\Http;

class FetchCollegeFootballEloRatings extends Command
{
    protected $signature = 'fetch:college-football-elo-ratings {year=2023}';
    protected $description = 'Fetch college football Elo ratings from the API and save to database';

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
        ])->get("https://api.collegefootballdata.com/ratings/elo?year={$year}");

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

                // Store the Elo rating data
                CollegeFootballEloRating::updateOrCreate(
                    [
                        'year' => $team['year'],
                        'team_id' => $teamRecord->id,
                    ],
                    [
                        'conference_id' => $conferenceRecord->id ?? null,
                        'elo' => $team['elo'] ?? null,
                    ]
                );
            }

            $this->info("College football Elo ratings for year {$year} fetched and saved successfully.");
        } else {
            $this->error('Failed to fetch data from the API.');
        }
    }
}
