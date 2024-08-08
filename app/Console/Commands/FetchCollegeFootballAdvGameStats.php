<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CollegeFootballAdvGameStat;
use Illuminate\Support\Facades\Http;

class FetchCollegeFootballAdvGameStats extends Command
{
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
                CollegeFootballAdvGameStat::updateOrCreate(
                    [
                        'game_id' => $stat['gameId']
                    ],
                    [
                        'season' => $year,
                        'week' => $stat['week'],
                        'team' => $stat['team'],
                        'opponent' => $stat['opponent'],
                        'offense_plays' => $stat['offense']['plays'] ?? null,
                        'offense_drives' => $stat['offense']['drives'] ?? null,
                        'offense_ppa' => $stat['offense']['ppa'] ?? null,
                        'offense_total_ppa' => $stat['offense']['totalPPA'] ?? null,
                        'offense_success_rate' => $stat['offense']['successRate'] ?? null,
                        'offense_explosiveness' => $stat['offense']['explosiveness'] ?? null,
                        'offense_power_success' => $stat['offense']['powerSuccess'] ?? null,
                        'offense_stuff_rate' => $stat['offense']['stuffRate'] ?? null,
                        'offense_line_yards' => $stat['offense']['lineYards'] ?? null,
                        'offense_line_yards_total' => $stat['offense']['lineYardsTotal'] ?? null,
                        'offense_second_level_yards' => $stat['offense']['secondLevelYards'] ?? null,
                        'offense_second_level_yards_total' => $stat['offense']['secondLevelYardsTotal'] ?? null,
                        'offense_open_field_yards' => $stat['offense']['openFieldYards'] ?? null,
                        'offense_open_field_yards_total' => $stat['offense']['openFieldYardsTotal'] ?? null,
                        'offense_standard_downs_ppa' => $stat['offense']['standardDowns']['ppa'] ?? null,
                        'offense_standard_downs_success_rate' => $stat['offense']['standardDowns']['successRate'] ?? null,
                        'offense_standard_downs_explosiveness' => $stat['offense']['standardDowns']['explosiveness'] ?? null,
                        'offense_passing_downs_ppa' => $stat['offense']['passingDowns']['ppa'] ?? null,
                        'offense_passing_downs_success_rate' => $stat['offense']['passingDowns']['successRate'] ?? null,
                        'offense_passing_downs_explosiveness' => $stat['offense']['passingDowns']['explosiveness'] ?? null,
                        'offense_rushing_plays_ppa' => $stat['offense']['rushingPlays']['ppa'] ?? null,
                        'offense_rushing_plays_total_ppa' => $stat['offense']['rushingPlays']['totalPPA'] ?? null,
                        'offense_rushing_plays_success_rate' => $stat['offense']['rushingPlays']['successRate'] ?? null,
                        'offense_rushing_plays_explosiveness' => $stat['offense']['rushingPlays']['explosiveness'] ?? null,
                        'offense_passing_plays_ppa' => $stat['offense']['passingPlays']['ppa'] ?? null,
                        'offense_passing_plays_total_ppa' => $stat['offense']['passingPlays']['totalPPA'] ?? null,
                        'offense_passing_plays_success_rate' => $stat['offense']['passingPlays']['successRate'] ?? null,
                        'offense_passing_plays_explosiveness' => $stat['offense']['passingPlays']['explosiveness'] ?? null
                    ]
                );
            }

            $this->info('College football advanced game stats fetched and stored successfully.');
        } else {
            $this->error('Failed to fetch the data.');
        }
    }
}
