<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CollegeFootballEloRating;
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
            'Authorization' => 'Bearer 4b/N6meGdvO3k52FMU375HldXVcg+iNk6o/SMYATiNL3LUkg0LNRcvUKg97pbGrT',
        ])->get("https://api.collegefootballdata.com/ratings/elo?year={$year}");

        if ($response->successful()) {
            $teams = $response->json();

            foreach ($teams as $team) {
                CollegeFootballEloRating::updateOrCreate(
                    [
                        'year' => $team['year'],
                        'team' => $team['team']
                    ],
                    [
                        'conference' => $team['conference'] ?? null,
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
