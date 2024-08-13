<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CollegeFootballGamePpa;
use App\Models\CollegeFootballTeam;
use App\Models\CollegeFootballConference;
use App\Models\CollegeFootballGame;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
            'Authorization' => 'Bearer ' . env('COLLEGE_FOOTBALL_DATA_API_KEY'),
        ])->get("https://api.collegefootballdata.com/ppa/games?year={$year}&seasonType={$seasonType}");

        if ($response->successful()) {
            $games = $response->json();

            foreach ($games as $game) {
                // Find the team and opponent by school name
                $team = CollegeFootballTeam::where('school', $game['team'])->first();
                $opponent = CollegeFootballTeam::where('school', $game['opponent'])->first();

                // Find the conference by name
                $conference = CollegeFootballConference::where('name', $game['conference'])->first();

                // Find the related game in the college_football_games table
                $collegeGame = CollegeFootballGame::where('id', $game['gameId'])->first();

                if (!$team || !$opponent || !$conference || !$collegeGame) {
                    // Log an error if any required data is missing
                    Log::error("Missing data for game ID {$game['gameId']}: Team, Opponent, Conference, or College Game not found.");
                    continue;
                }

                CollegeFootballGamePpa::updateOrCreate(
                    ['game_id' => $collegeGame->id], // Link to college_football_games table
                    [
                        'season' => $game['season'],
                        'week' => $game['week'],
                        'team_id' => $team->id, // Use id from college_football_teams table
                        'conference_id' => $conference->id, // Use id from college_football_conferences table
                        'opponent_id' => $opponent->id, // Use id from college_football_teams table
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
