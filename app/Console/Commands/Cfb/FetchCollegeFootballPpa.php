<?php

namespace App\Console\Commands\Cfb;

use App\Models\CollegeFootballPpa;
use App\Models\CollegeFootballTeam;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchCollegeFootballPpa extends Command
{
    protected $signature = 'fetch:college-football-ppa {year=2023}';
    protected $description = 'Fetch college football PPA data from the API and save to database';

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
        ])->get("https://api.collegefootballdata.com/ppa/teams?year={$year}");

        if ($response->successful()) {
            $teams = $response->json();

            foreach ($teams as $teamData) {
                // Log the team name being processed
                Log::info('Processing team: ' . $teamData['team']);

                // Find the corresponding team or create it if it doesn't exist
                $team = CollegeFootballTeam::firstOrCreate(
                    ['school' => $teamData['team']],
                    ['school' => $teamData['team']]
                );

                // Log the team ID
                Log::info('Team ID for ' . $teamData['team'] . ': ' . $team->id);

                // Store the PPA data
                CollegeFootballPpa::updateOrCreate(
                    [
                        'team_id' => $team->id,
                        'season' => $teamData['season'],
                    ],
                    [
                        'conference' => $teamData['conference'] ?? null,
                        'offense_overall' => $teamData['offense']['overall'] ?? null,
                        'offense_passing' => $teamData['offense']['passing'] ?? null,
                        'offense_rushing' => $teamData['offense']['rushing'] ?? null,
                        'offense_first_down' => $teamData['offense']['firstDown'] ?? null,
                        'offense_second_down' => $teamData['offense']['secondDown'] ?? null,
                        'offense_third_down' => $teamData['offense']['thirdDown'] ?? null,
                        'offense_cumulative_total' => $teamData['offense']['cumulative']['total'] ?? null,
                        'offense_cumulative_passing' => $teamData['offense']['cumulative']['passing'] ?? null,
                        'offense_cumulative_rushing' => $teamData['offense']['cumulative']['rushing'] ?? null,
                        'defense_overall' => $teamData['defense']['overall'] ?? null,
                        'defense_passing' => $teamData['defense']['passing'] ?? null,
                        'defense_rushing' => $teamData['defense']['rushing'] ?? null,
                        'defense_first_down' => $teamData['defense']['firstDown'] ?? null,
                        'defense_second_down' => $teamData['defense']['secondDown'] ?? null,
                        'defense_third_down' => $teamData['defense']['thirdDown'] ?? null,
                        'defense_cumulative_total' => $teamData['defense']['cumulative']['total'] ?? null,
                        'defense_cumulative_passing' => $teamData['defense']['cumulative']['passing'] ?? null,
                        'defense_cumulative_rushing' => $teamData['defense']['cumulative']['rushing'] ?? null,
                    ]
                );

                // Log success for each team
                Log::info('PPA data stored successfully for team: ' . $teamData['team']);
            }

            $this->info("College football PPA data for year {$year} fetched and saved successfully.");
        } else {
            $this->error('Failed to fetch data from the API.');
        }
    }
}
