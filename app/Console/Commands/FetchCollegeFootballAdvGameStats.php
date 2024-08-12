<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CollegeFootballAdvGameStat;
use App\Models\CollegeFootballTeam;
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
                    [
                        'season' => $year,
                        'week' => $stat['week'],
                        'opponent_id' => $opponent->id,
                        // Offense stats
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
                        'offense_passing_plays_explosiveness' => $stat['offense']['passingPlays']['explosiveness'] ?? null,
                        // Defense stats
                        'defense_plays' => $stat['defense']['plays'] ?? null,
                        'defense_drives' => $stat['defense']['drives'] ?? null,
                        'defense_ppa' => $stat['defense']['ppa'] ?? null,
                        'defense_total_ppa' => $stat['defense']['totalPPA'] ?? null,
                        'defense_success_rate' => $stat['defense']['successRate'] ?? null,
                        'defense_explosiveness' => $stat['defense']['explosiveness'] ?? null,
                        'defense_power_success' => $stat['defense']['powerSuccess'] ?? null,
                        'defense_stuff_rate' => $stat['defense']['stuffRate'] ?? null,
                        'defense_line_yards' => $stat['defense']['lineYards'] ?? null,
                        'defense_line_yards_total' => $stat['defense']['lineYardsTotal'] ?? null,
                        'defense_second_level_yards' => $stat['defense']['secondLevelYards'] ?? null,
                        'defense_second_level_yards_total' => $stat['defense']['secondLevelYardsTotal'] ?? null,
                        'defense_open_field_yards' => $stat['defense']['openFieldYards'] ?? null,
                        'defense_open_field_yards_total' => $stat['defense']['openFieldYardsTotal'] ?? null,
                        'defense_standard_downs_ppa' => $stat['defense']['standardDowns']['ppa'] ?? null,
                        'defense_standard_downs_success_rate' => $stat['defense']['standardDowns']['successRate'] ?? null,
                        'defense_standard_downs_explosiveness' => $stat['defense']['standardDowns']['explosiveness'] ?? null,
                        'defense_passing_downs_ppa' => $stat['defense']['passingDowns']['ppa'] ?? null,
                        'defense_passing_downs_success_rate' => $stat['defense']['passingDowns']['successRate'] ?? null,
                        'defense_passing_downs_explosiveness' => $stat['defense']['passingDowns']['explosiveness'] ?? null,
                        'defense_rushing_plays_ppa' => $stat['defense']['rushingPlays']['ppa'] ?? null,
                        'defense_rushing_plays_total_ppa' => $stat['defense']['rushingPlays']['totalPPA'] ?? null,
                        'defense_rushing_plays_success_rate' => $stat['defense']['rushingPlays']['successRate'] ?? null,
                        'defense_rushing_plays_explosiveness' => $stat['defense']['rushingPlays']['explosiveness'] ?? null,
                        'defense_passing_plays_ppa' => $stat['defense']['passingPlays']['ppa'] ?? null,
                        'defense_passing_plays_total_ppa' => $stat['defense']['passingPlays']['totalPPA'] ?? null,
                        'defense_passing_plays_success_rate' => $stat['defense']['passingPlays']['successRate'] ?? null,
                        'defense_passing_plays_explosiveness' => $stat['defense']['passingPlays']['explosiveness'] ?? null,
                    ]
                );

                // Create or update record for the opponent
                CollegeFootballAdvGameStat::updateOrCreate(
                    [
                        'game_id' => $stat['gameId'],
                        'team_id' => $opponent->id,
                    ],
                    [
                        'season' => $year,
                        'week' => $stat['week'],
                        'opponent_id' => $team->id,
                        // Offense stats for opponent (which are defense stats for the team)
                        'offense_plays' => $stat['defense']['plays'] ?? null,
                        'offense_drives' => $stat['defense']['drives'] ?? null,
                        'offense_ppa' => $stat['defense']['ppa'] ?? null,
                        'offense_total_ppa' => $stat['defense']['totalPPA'] ?? null,
                        'offense_success_rate' => $stat['defense']['successRate'] ?? null,
                        'offense_explosiveness' => $stat['defense']['explosiveness'] ?? null,
                        'offense_power_success' => $stat['defense']['powerSuccess'] ?? null,
                        'offense_stuff_rate' => $stat['defense']['stuffRate'] ?? null,
                        'offense_line_yards' => $stat['defense']['lineYards'] ?? null,
                        'offense_line_yards_total' => $stat['defense']['lineYardsTotal'] ?? null,
                        'offense_second_level_yards' => $stat['defense']['secondLevelYards'] ?? null,
                        'offense_second_level_yards_total' => $stat['defense']['secondLevelYardsTotal'] ?? null,
                        'offense_open_field_yards' => $stat['defense']['openFieldYards'] ?? null,
                        'offense_open_field_yards_total' => $stat['defense']['openFieldYardsTotal'] ?? null,
                        'offense_standard_downs_ppa' => $stat['defense']['standardDowns']['ppa'] ?? null,
                        'offense_standard_downs_success_rate' => $stat['defense']['standardDowns']['successRate'] ?? null,
                        'offense_standard_downs_explosiveness' => $stat['defense']['standardDowns']['explosiveness'] ?? null,
                        'offense_passing_downs_ppa' => $stat['defense']['passingDowns']['ppa'] ?? null,
                        'offense_passing_downs_success_rate' => $stat['defense']['passingDowns']['successRate'] ?? null,
                        'offense_passing_downs_explosiveness' => $stat['defense']['passingDowns']['explosiveness'] ?? null,
                        'offense_rushing_plays_ppa' => $stat['defense']['rushingPlays']['ppa'] ?? null,
                        'offense_rushing_plays_total_ppa' => $stat['defense']['rushingPlays']['totalPPA'] ?? null,
                        'offense_rushing_plays_success_rate' => $stat['defense']['rushingPlays']['successRate'] ?? null,
                        'offense_rushing_plays_explosiveness' => $stat['defense']['rushingPlays']['explosiveness'] ?? null,
                        'offense_passing_plays_ppa' => $stat['defense']['passingPlays']['ppa'] ?? null,
                        'offense_passing_plays_total_ppa' => $stat['defense']['passingPlays']['totalPPA'] ?? null,
                        'offense_passing_plays_success_rate' => $stat['defense']['passingPlays']['successRate'] ?? null,
                        'offense_passing_plays_explosiveness' => $stat['defense']['passingPlays']['explosiveness'] ?? null,
                        // Defense stats for opponent (which are offense stats for the team)
                        'defense_plays' => $stat['offense']['plays'] ?? null,
                        'defense_drives' => $stat['offense']['drives'] ?? null,
                        'defense_ppa' => $stat['offense']['ppa'] ?? null,
                        'defense_total_ppa' => $stat['offense']['totalPPA'] ?? null,
                        'defense_success_rate' => $stat['offense']['successRate'] ?? null,
                        'defense_explosiveness' => $stat['offense']['explosiveness'] ?? null,
                        'defense_power_success' => $stat['offense']['powerSuccess'] ?? null,
                        'defense_stuff_rate' => $stat['offense']['stuffRate'] ?? null,
                        'defense_line_yards' => $stat['offense']['lineYards'] ?? null,
                        'defense_line_yards_total' => $stat['offense']['lineYardsTotal'] ?? null,
                        'defense_second_level_yards' => $stat['offense']['secondLevelYards'] ?? null,
                        'defense_second_level_yards_total' => $stat['offense']['secondLevelYardsTotal'] ?? null,
                        'defense_open_field_yards' => $stat['offense']['openFieldYards'] ?? null,
                        'defense_open_field_yards_total' => $stat['offense']['openFieldYardsTotal'] ?? null,
                        'defense_standard_downs_ppa' => $stat['offense']['standardDowns']['ppa'] ?? null,
                        'defense_standard_downs_success_rate' => $stat['offense']['standardDowns']['successRate'] ?? null,
                        'defense_standard_downs_explosiveness' => $stat['offense']['standardDowns']['explosiveness'] ?? null,
                        'defense_passing_downs_ppa' => $stat['offense']['passingDowns']['ppa'] ?? null,
                        'defense_passing_downs_success_rate' => $stat['offense']['passingDowns']['successRate'] ?? null,
                        'defense_passing_downs_explosiveness' => $stat['offense']['passingDowns']['explosiveness'] ?? null,
                        'defense_rushing_plays_ppa' => $stat['offense']['rushingPlays']['ppa'] ?? null,
                        'defense_rushing_plays_total_ppa' => $stat['offense']['rushingPlays']['totalPPA'] ?? null,
                        'defense_rushing_plays_success_rate' => $stat['offense']['rushingPlays']['successRate'] ?? null,
                        'defense_rushing_plays_explosiveness' => $stat['offense']['rushingPlays']['explosiveness'] ?? null,
                        'defense_passing_plays_ppa' => $stat['offense']['passingPlays']['ppa'] ?? null,
                        'defense_passing_plays_total_ppa' => $stat['offense']['passingPlays']['totalPPA'] ?? null,
                        'defense_passing_plays_success_rate' => $stat['offense']['passingPlays']['successRate'] ?? null,
                        'defense_passing_plays_explosiveness' => $stat['offense']['passingPlays']['explosiveness'] ?? null,
                    ]
                );
            }

            $this->info('College football advanced game stats fetched and stored successfully.');
        } else {
            $this->error('Failed to fetch the data.');
        }
    }
}
