<?php

namespace App\Console\Commands\Cfb;

use App\Models\CollegeFootballConference;
use App\Models\CollegeFootballSpRating;
use App\Models\CollegeFootballTeam;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchCollegeFootballSpRatings extends Command
{
    protected $signature = 'fetch:college-football-sp-ratings {year=2024}';
    protected $description = 'Fetch college football SP ratings from the API and save to database';

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
        ])->get("https://api.collegefootballdata.com/ratings/sp?year={$year}");

        if ($response->successful()) {
            $teams = $response->json();

            foreach ($teams as $teamData) {
                // Find the corresponding team
                $team = CollegeFootballTeam::where('school', $teamData['team'])->first();
                if (!$team) {
                    $this->error('Team not found for: ' . $teamData['team']);
                    continue;
                }

                // Find the corresponding conference
                $conference = CollegeFootballConference::where('abbreviation', $teamData['conference'])
                    ->orWhere('name', $teamData['conference'])
                    ->first();

                if (!$conference) {
                    $this->error('Conference not found for: ' . json_encode($teamData['conference']));
                    continue;
                }

                // Update or create the SP rating with team_id and conference_id
                CollegeFootballSpRating::updateOrCreate(
                    [
                        'year' => $teamData['year'],
                        'team_id' => $team->id ?? null,
                    ],
                    [
                        'conference_id' => $conference->id ?? null,
                        'rating' => $teamData['rating'] ?? null,
                        'ranking' => $teamData['ranking'] ?? null,
                        'second_order_wins' => $teamData['secondOrderWins'] ?? null,
                        'sos' => $teamData['sos'] ?? null,
                        'offense_ranking' => $teamData['offense']['ranking'] ?? null,
                        'offense_rating' => $teamData['offense']['rating'] ?? null,
                        'offense_success' => $teamData['offense']['success'] ?? null,
                        'offense_explosiveness' => $teamData['offense']['explosiveness'] ?? null,
                        'offense_rushing' => $teamData['offense']['rushing'] ?? null,
                        'offense_passing' => $teamData['offense']['passing'] ?? null,
                        'offense_standard_downs' => $teamData['offense']['standardDowns'] ?? null,
                        'offense_passing_downs' => $teamData['offense']['passingDowns'] ?? null,
                        'offense_run_rate' => $teamData['offense']['runRate'] ?? null,
                        'offense_pace' => $teamData['offense']['pace'] ?? null,
                        'defense_ranking' => $teamData['defense']['ranking'] ?? null,
                        'defense_rating' => $teamData['defense']['rating'] ?? null,
                        'defense_success' => $teamData['defense']['success'] ?? null,
                        'defense_explosiveness' => $teamData['defense']['explosiveness'] ?? null,
                        'defense_rushing' => $teamData['defense']['rushing'] ?? null,
                        'defense_passing' => $teamData['defense']['passing'] ?? null,
                        'defense_standard_downs' => $teamData['defense']['standardDowns'] ?? null,
                        'defense_passing_downs' => $teamData['defense']['passingDowns'] ?? null,
                        'defense_havoc_total' => $teamData['defense']['havoc']['total'] ?? null,
                        'defense_havoc_front_seven' => $teamData['defense']['havoc']['frontSeven'] ?? null,
                        'defense_havoc_db' => $teamData['defense']['havoc']['db'] ?? null,
                        'special_teams_rating' => $teamData['specialTeams']['rating'] ?? null,
                    ]
                );
            }

            $this->info("College football SP ratings for year {$year} fetched and saved successfully.");
        } else {
            $this->error('Failed to fetch data from the API.');
        }
    }
}
