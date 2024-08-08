<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CollegeFootballPregame;
use Illuminate\Support\Facades\Http;

class FetchCollegeFootballPregame extends Command
{
    protected $signature = 'fetch:college-football-pregame {year=2023} {week=1}';
    protected $description = 'Fetch college football pregame win probability data from the API and save to database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $year = $this->argument('year');
        $week = $this->argument('week');

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'Authorization' => 'Bearer 4b/N6meGdvO3k52FMU375HldXVcg+iNk6o/SMYATiNL3LUkg0LNRcvUKg97pbGrT',
        ])->get("https://api.collegefootballdata.com/metrics/wp/pregame?year={$year}&week={$week}");

        if ($response->successful()) {
            $games = $response->json();

            foreach ($games as $game) {
                CollegeFootballPregame::updateOrCreate(
                    [
                        'season' => $game['season'],
                        'season_type' => $game['seasonType'],
                        'week' => $game['week'],
                        'game_id' => $game['gameId']
                    ],
                    [
                        'home_team' => $game['homeTeam'],
                        'away_team' => $game['awayTeam'],
                        'spread' => $game['spread'],
                        'home_win_prob' => $game['homeWinProb'],
                    ]
                );
            }

            $this->info("College football pregame win probability data for year {$year}, week {$week} fetched and saved successfully.");
        } else {
            $this->error('Failed to fetch data from the API.');
        }
    }
}
