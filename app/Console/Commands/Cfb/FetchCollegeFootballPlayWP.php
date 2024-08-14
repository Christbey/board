<?php

namespace App\Console\Commands\Cfb;

use App\Models\CollegeFootballGame;
use App\Models\CollegeFootballPlayWP;
use App\Models\CollegeFootballTeam;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchCollegeFootballPlayWP extends Command
{
    protected $signature = 'fetch:college-football-play-wp {gameId}';
    protected $description = 'Fetch play win probability data from the API and save to the database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $gameId = $this->argument('gameId');

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'Authorization' => 'Bearer ' . env('COLLEGE_FOOTBALL_DATA_API_KEY'),
        ])->get("https://api.collegefootballdata.com/metrics/wp?gameId={$gameId}");

        if ($response->successful()) {
            $plays = $response->json();

            foreach ($plays as $play) {
                // Find or create the home and away teams
                $homeTeam = CollegeFootballTeam::firstOrCreate(
                    ['school' => $play['home']],
                    ['school' => $play['home']]
                );

                $awayTeam = CollegeFootballTeam::firstOrCreate(
                    ['school' => $play['away']],
                    ['school' => $play['away']]
                );

                // Find or create the game
                $game = CollegeFootballGame::firstOrCreate(
                    ['id' => $gameId],
                    [
                        'season' => $play['season'] ?? null,
                        'week' => $play['week'] ?? null,
                        'season_type' => $play['seasonType'] ?? null,
                        'start_date' => $play['startDate'] ?? null,
                        'home_id' => $homeTeam->id,
                        'home_team' => $play['home'],
                        'away_id' => $awayTeam->id,
                        'away_team' => $play['away'],
                    ]
                );

                // Store the play win probability data
                CollegeFootballPlayWP::updateOrCreate(
                    [
                        'game_id' => $game->id,
                        'play_id' => $play['playId'],
                    ],
                    [
                        'play_text' => $play['playText'],
                        'home_id' => $homeTeam->id,
                        'home' => $play['home'],
                        'away_id' => $awayTeam->id,
                        'away' => $play['away'],
                        'spread' => $play['spread'],
                        'home_ball' => $play['homeBall'],
                        'home_score' => $play['homeScore'],
                        'away_score' => $play['awayScore'],
                        'time_remaining' => $play['timeRemaining'] ?? null,
                        'yard_line' => $play['yardLine'] ?? null,
                        'down' => $play['down'],
                        'distance' => $play['distance'],
                        'home_win_prob' => $play['homeWinProb'],
                        'play_number' => $play['playNumber'],
                    ]
                );
            }

            $this->info("Play win probability data for game ID {$gameId} fetched and saved successfully.");
        } else {
            $this->error('Failed to fetch data from the API.');
        }
    }
}
