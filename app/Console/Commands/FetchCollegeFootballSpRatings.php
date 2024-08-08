<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CollegeFootballSpRating;
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
            'Authorization' => 'Bearer 4b/N6meGdvO3k52FMU375HldXVcg+iNk6o/SMYATiNL3LUkg0LNRcvUKg97pbGrT',
        ])->get("https://api.collegefootballdata.com/ratings/sp?year={$year}");

        if ($response->successful()) {
            $teams = $response->json();

            foreach ($teams as $team) {
                CollegeFootballSpRating::updateOrCreate(
                    [
                        'year' => $team['year'],
                        'team' => $team['team']
                    ],
                    [
                        'conference' => $team['conference'] ?? null,
                        'rating' => $team['rating'] ?? null,
                        'ranking' => $team['ranking'] ?? null,
                        'second_order_wins' => $team['secondOrderWins'] ?? null,
                        'sos' => $team['sos'] ?? null,
                        'offense_ranking' => $team['offense']['ranking'] ?? null,
                        'offense_rating' => $team['offense']['rating'] ?? null,
                        'offense_success' => $team['offense']['success'] ?? null,
                        'offense_explosiveness' => $team['offense']['explosiveness'] ?? null,
                        'offense_rushing' => $team['offense']['rushing'] ?? null,
                        'offense_passing' => $team['offense']['passing'] ?? null,
                        'offense_standard_downs' => $team['offense']['standardDowns'] ?? null,
                        'offense_passing_downs' => $team['offense']['passingDowns'] ?? null,
                        'offense_run_rate' => $team['offense']['runRate'] ?? null,
                        'offense_pace' => $team['offense']['pace'] ?? null,
                        'defense_ranking' => $team['defense']['ranking'] ?? null,
                        'defense_rating' => $team['defense']['rating'] ?? null,
                        'defense_success' => $team['defense']['success'] ?? null,
                        'defense_explosiveness' => $team['defense']['explosiveness'] ?? null,
                        'defense_rushing' => $team['defense']['rushing'] ?? null,
                        'defense_passing' => $team['defense']['passing'] ?? null,
                        'defense_standard_downs' => $team['defense']['standardDowns'] ?? null,
                        'defense_passing_downs' => $team['defense']['passingDowns'] ?? null,
                        'defense_havoc_total' => $team['defense']['havoc']['total'] ?? null,
                        'defense_havoc_front_seven' => $team['defense']['havoc']['frontSeven'] ?? null,
                        'defense_havoc_db' => $team['defense']['havoc']['db'] ?? null,
                        'special_teams_rating' => $team['specialTeams']['rating'] ?? null,
                    ]
                );
            }

            $this->info("College football SP ratings for year {$year} fetched and saved successfully.");
        } else {
            $this->error('Failed to fetch data from the API.');
        }
    }
}
