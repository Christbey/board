<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CollegeFootballGamePpa;
use Illuminate\Support\Facades\Http;

class FetchCollegeFootballGamePpa extends Command
{
    protected $signature = 'fetch:college-football-game-ppa {year=2023} {seasonType=regular}';
    protected $description = 'Fetch college football game PPA data from the API and save to database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $year = $this->argument('year');
        $seasonType = $this->argument('seasonType');

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'Authorization' => 'Bearer 4b/N6meGdvO3k52FMU375HldXVcg+iNk6o/SMYATiNL3LUkg0LNRcvUKg97pbGrT',
        ])->get("https://api.collegefootballdata.com/ppa/games?year={$year}&seasonType={$seasonType}");

        if ($response->successful()) {
            $games = $response->json();

            foreach ($games as $game) {
                CollegeFootballGamePpa::updateOrCreate(
                    ['game_id' => $game['gameId']],
                    [
                        'season' => $game['season'],
                        'week' => $game['week'],
                        'team' => $game['team'],
                        'conference' => $game['conference'],
                        'opponent' => $game['opponent'],
                        'offense_overall' => $game['offense']['overall'] ?? null,
                        'offense_passing' => $game['offense']['passing'] ?? null,
                        'offense_rushing' => $game['offense']['rushing'] ?? null,
                        'offense_first_down' => $game['offense']['firstDown'] ?? null,
                        'offense_second_down' => $game['offense']['secondDown'] ?? null,
                        'offense_third_down' => $game['offense']['thirdDown'] ?? null,
                        'defense_overall' => $game['defense']['overall'] ?? null,
                        'defense_passing' => $game['defense']['passing'] ?? null,
                        'defense_rushing' => $game['defense']['rushing'] ?? null,
                        'defense_first_down' => $game['defense']['firstDown'] ?? null,
                        'defense_second_down' => $game['defense']['secondDown'] ?? null,
                        'defense_third_down' => $game['defense']['thirdDown'] ?? null,
                    ]
                );
            }

            $this->info("College football game PPA data for year {$year}, season type {$seasonType} fetched and saved successfully.");
        } else {
            $this->error('Failed to fetch data from the API.');
        }
    }
}
