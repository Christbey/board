<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CollegeFootballPlayWP;
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
            'Authorization' => 'Bearer 4b/N6meGdvO3k52FMU375HldXVcg+iNk6o/SMYATiNL3LUkg0LNRcvUKg97pbGrT',
        ])->get("https://api.collegefootballdata.com/metrics/wp?gameId={$gameId}");

        if ($response->successful()) {
            $plays = $response->json();

            foreach ($plays as $play) {
                CollegeFootballPlayWP::updateOrCreate(
                    [
                        'game_id' => $gameId,
                        'play_id' => $play['playId'],
                    ],
                    [
                        'play_text' => $play['playText'],
                        'home_id' => $play['homeId'],
                        'home' => $play['home'],
                        'away_id' => $play['awayId'],
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
