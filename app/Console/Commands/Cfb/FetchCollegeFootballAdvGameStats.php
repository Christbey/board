<?php

namespace App\Console\Commands\Cfb;

use App\Models\CollegeFootballAdvGameStat;
use App\Models\CollegeFootballTeam;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Traits\CollegeFootballAdvGameTrait;

class FetchCollegeFootballAdvGameStats extends Command
{
    use CollegeFootballAdvGameTrait;

    protected $signature = 'fetch:college-football-adv-game-stats {week?}';
    protected $description = 'Fetch and store college football advanced game stats from API';

    public function handle()
    {
        $year = 2023; // Adjust this as needed
        $week = $this->argument('week');

        $url = "https://api.collegefootballdata.com/stats/game/advanced?year=$year";
        if ($week) {
            $url .= "&week=$week";
        }

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'Authorization' => 'Bearer ' . env('COLLEGE_FOOTBALL_DATA_API_KEY')
        ])->get($url);

        if ($response->successful()) {
            $stats = $response->json();

            foreach ($stats as $stat) {
                // Find or create the team
                $team = CollegeFootballTeam::firstOrCreate(
                    ['school' => $stat['team']],
                    ['school' => $stat['team']]
                );

                // Find or create the opponent
                $opponent = CollegeFootballTeam::firstOrCreate(
                    ['school' => $stat['opponent']],
                    ['school' => $stat['opponent']]
                );

                // Create or update record for the team
                CollegeFootballAdvGameStat::updateOrCreate(
                    [
                        'game_id' => $stat['gameId'],
                        'team_id' => $team->id,
                    ],
                    array_merge(
                        [
                            'season' => $year,
                            'week' => $stat['week'],
                            'opponent_id' => $opponent->id,
                        ],
                        $this->extractOffenseStats($stat['offense']),
                        $this->extractDefenseStats($stat['defense'])
                    )
                );

                // Create or update record for the opponent
                CollegeFootballAdvGameStat::updateOrCreate(
                    [
                        'game_id' => $stat['gameId'],
                        'team_id' => $opponent->id,
                    ],
                    array_merge(
                        [
                            'season' => $year,
                            'week' => $stat['week'],
                            'opponent_id' => $team->id,
                        ],
                        $this->reverseStat($stat)
                    )
                );
            }

            $this->info('College football advanced game stats fetched and stored successfully.');
        } else {
            $this->error('Failed to fetch the data.');
        }
    }
}
