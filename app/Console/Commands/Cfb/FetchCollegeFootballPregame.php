<?php

namespace App\Console\Commands\Cfb;

use App\Models\CollegeFootballPregame;
use App\Models\CollegeFootballTeam;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchCollegeFootballPregame extends Command
{
    protected $signature = 'fetch:college-football-pregame {year=2024} {week=1}';
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
            'Authorization' => 'Bearer ' . env('COLLEGE_FOOTBALL_DATA_API_KEY'),
        ])->get("https://api.collegefootballdata.com/metrics/wp/pregame?year={$year}&week={$week}");

        if ($response->successful()) {
            $games = $response->json();

            foreach ($games as $game) {
                // Find the corresponding teams by name
                $homeTeam = CollegeFootballTeam::where('school', $game['homeTeam'])->first();
                $awayTeam = CollegeFootballTeam::where('school', $game['awayTeam'])->first();

                if (!$homeTeam || !$awayTeam) {
                    $this->error('Team not found for game: ' . $game['gameId']);
                    continue;
                }

                CollegeFootballPregame::updateOrCreate(
                    [
                        'game_id' => $game['gameId']
                    ],
                    [
                        'home_team_id' => $homeTeam->id,
                        'away_team_id' => $awayTeam->id,
                        'spread' => $game['spread'],
                        'home_win_prob' => $game['homeWinProb'],
                        'season_type' => $game['seasonType'],
                        'season' => $game['season'],
                        'week' => $game['week'],
                    ]
                );
            }

            $this->info("College football pregame win probability data for year {$year}, week {$week} fetched and saved successfully.");
        } else {
            $this->error('Failed to fetch data from the API.');
        }
    }
}
