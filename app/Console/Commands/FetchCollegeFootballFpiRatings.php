<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CollegeFootballFpiRating;
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
            'Authorization' => 'Bearer 4b/N6meGdvO3k52FMU375HldXVcg+iNk6o/SMYATiNL3LUkg0LNRcvUKg97pbGrT',
        ])->get("https://api.collegefootballdata.com/ratings/fpi?year={$year}");

        if ($response->successful()) {
            $teams = $response->json();

            foreach ($teams as $team) {
                CollegeFootballFpiRating::updateOrCreate(
                    [
                        'year' => $team['year'],
                        'team' => $team['team']
                    ],
                    [
                        'conference' => $team['conference'] ?? null,
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
