<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CollegeFootballGame;
use Illuminate\Support\Facades\Http;

class FetchCollegeFootballGames extends Command
{
    protected $signature = 'fetch:college-football-games {year=2024} {seasonType=regular}';
    protected $description = 'Fetch college football games from the API and save to database';

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
        ])->get("https://api.collegefootballdata.com/games?year={$year}&seasonType={$seasonType}");

        if ($response->successful()) {
            $games = $response->json();

            foreach ($games as $game) {
                CollegeFootballGame::updateOrCreate(
                    ['id' => $game['id']],
                    [
                        'season' => $game['season'],
                        'week' => $game['week'],
                        'season_type' => $game['season_type'],
                        'start_date' => $game['start_date'],
                        'start_time_tbd' => $game['start_time_tbd'],
                        'completed' => $game['completed'],
                        'neutral_site' => $game['neutral_site'],
                        'conference_game' => $game['conference_game'],
                        'attendance' => $game['attendance'] ?? null,
                        'venue_id' => $game['venue_id'],
                        'venue' => $game['venue'],
                        'home_id' => $game['home_id'],
                        'home_team' => $game['home_team'],
                        'home_conference' => $game['home_conference'],
                        'home_division' => $game['home_division'],
                        'home_points' => $game['home_points'] ?? null,
                        'home_line_scores' => $game['home_line_scores'] ?? null,
                        'home_post_win_prob' => $game['home_post_win_prob'] ?? null,
                        'home_pregame_elo' => $game['home_pregame_elo'] ?? null,
                        'home_postgame_elo' => $game['home_postgame_elo'] ?? null,
                        'away_id' => $game['away_id'],
                        'away_team' => $game['away_team'],
                        'away_conference' => $game['away_conference'] ?? null,
                        'away_division' => $game['away_division'] ?? null,
                        'away_points' => $game['away_points'] ?? null,
                        'away_line_scores' => $game['away_line_scores'] ?? null,
                        'away_post_win_prob' => $game['away_post_win_prob'] ?? null,
                        'away_pregame_elo' => $game['away_pregame_elo'] ?? null,
                        'away_postgame_elo' => $game['away_postgame_elo'] ?? null,
                        'excitement_index' => $game['excitement_index'] ?? null,
                        'highlights' => $game['highlights'] ?? null,
                        'notes' => $game['notes'] ?? null,
                    ]
                );
            }

            $this->info("College football games for year {$year}, season type {$seasonType} fetched and saved successfully.");
        } else {
            $this->error('Failed to fetch data from the API.');
        }
    }
}
