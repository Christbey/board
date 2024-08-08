<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CollegeFootballPpa;
use Illuminate\Support\Facades\Http;

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
            'Authorization' => 'Bearer 4b/N6meGdvO3k52FMU375HldXVcg+iNk6o/SMYATiNL3LUkg0LNRcvUKg97pbGrT',
        ])->get("https://api.collegefootballdata.com/ppa/teams?year={$year}");

        if ($response->successful()) {
            $teams = $response->json();

            foreach ($teams as $team) {
                CollegeFootballPpa::updateOrCreate(
                    [
                        'season' => $team['season'],
                        'team' => $team['team']
                    ],
                    [
                        'conference' => $team['conference'] ?? null,
                        'offense_overall' => $team['offense']['overall'] ?? null,
                        'offense_passing' => $team['offense']['passing'] ?? null,
                        'offense_rushing' => $team['offense']['rushing'] ?? null,
                        'offense_first_down' => $team['offense']['firstDown'] ?? null,
                        'offense_second_down' => $team['offense']['secondDown'] ?? null,
                        'offense_third_down' => $team['offense']['thirdDown'] ?? null,
                        'offense_cumulative_total' => $team['offense']['cumulative']['total'] ?? null,
                        'offense_cumulative_passing' => $team['offense']['cumulative']['passing'] ?? null,
                        'offense_cumulative_rushing' => $team['offense']['cumulative']['rushing'] ?? null,
                        'defense_overall' => $team['defense']['overall'] ?? null,
                        'defense_passing' => $team['defense']['passing'] ?? null,
                        'defense_rushing' => $team['defense']['rushing'] ?? null,
                        'defense_first_down' => $team['defense']['firstDown'] ?? null,
                        'defense_second_down' => $team['defense']['secondDown'] ?? null,
                        'defense_third_down' => $team['defense']['thirdDown'] ?? null,
                        'defense_cumulative_total' => $team['defense']['cumulative']['total'] ?? null,
                        'defense_cumulative_passing' => $team['defense']['cumulative']['passing'] ?? null,
                        'defense_cumulative_rushing' => $team['defense']['cumulative']['rushing'] ?? null,
                    ]
                );
            }

            $this->info("College football PPA data for year {$year} fetched and saved successfully.");
        } else {
            $this->error('Failed to fetch data from the API.');
        }
    }
}
