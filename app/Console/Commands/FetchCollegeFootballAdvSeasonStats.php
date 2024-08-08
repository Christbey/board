<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CollegeFootballAdvSeasonStat;
use Illuminate\Support\Facades\Http;

class FetchCollegeFootballAdvSeasonStats extends Command
{
    protected $signature = 'fetch:college-football-adv-season-stats';

    protected $description = 'Fetch and store college football advanced season stats from API';

    public function handle()
    {
        $url = 'https://api.collegefootballdata.com/stats/season/advanced?year=2023';
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'Authorization' => 'Bearer ' . env('COLLEGE_FOOTBALL_DATA_API_KEY')
        ])->get($url);

        if ($response->successful()) {
            $stats = $response->json();

            foreach ($stats as $stat) {
                CollegeFootballAdvSeasonStat::updateOrCreate(
                    [
                        'season' => $stat['season'],
                        'team' => $stat['team']
                    ],
                    [
                        'conference' => $stat['conference'],
                        'offense_plays' => $stat['offense']['plays'],
                        'offense_drives' => $stat['offense']['drives'],
                        'offense_ppa' => $stat['offense']['ppa'],
                        'offense_total_ppa' => $stat['offense']['totalPPA'],
                        'offense_success_rate' => $stat['offense']['successRate'],
                        'offense_explosiveness' => $stat['offense']['explosiveness'],
                        'offense_power_success' => $stat['offense']['powerSuccess'],
                        'offense_stuff_rate' => $stat['offense']['stuffRate'],
                        'offense_line_yards' => $stat['offense']['lineYards'],
                        'offense_line_yards_total' => $stat['offense']['lineYardsTotal'],
                        'offense_second_level_yards' => $stat['offense']['secondLevelYards'],
                        'offense_second_level_yards_total' => $stat['offense']['secondLevelYardsTotal'],
                        'offense_open_field_yards' => $stat['offense']['openFieldYards'],
                        'offense_open_field_yards_total' => $stat['offense']['openFieldYardsTotal'],
                        'offense_total_opportunities' => $stat['offense']['totalOpportunies'],
                        'offense_points_per_opportunity' => $stat['offense']['pointsPerOpportunity'],
                        'offense_field_position_average_start' => $stat['offense']['fieldPosition']['averageStart'],
                        'offense_field_position_average_predicted_points' => $stat['offense']['fieldPosition']['averagePredictedPoints'],
                        'offense_havoc_total' => $stat['offense']['havoc']['total'],
                        'offense_havoc_front_seven' => $stat['offense']['havoc']['frontSeven'],
                        'offense_havoc_db' => $stat['offense']['havoc']['db'],
                        'offense_standard_downs_rate' => $stat['offense']['standardDowns']['rate'],
                        'offense_standard_downs_ppa' => $stat['offense']['standardDowns']['ppa'],
                        'offense_standard_downs_success_rate' => $stat['offense']['standardDowns']['successRate'],
                        'offense_standard_downs_explosiveness' => $stat['offense']['standardDowns']['explosiveness'],
                        'offense_passing_downs_rate' => $stat['offense']['passingDowns']['rate'],
                        'offense_passing_downs_ppa' => $stat['offense']['passingDowns']['ppa'],
                        'offense_passing_downs_success_rate' => $stat['offense']['passingDowns']['successRate'],
                        'offense_passing_downs_explosiveness' => $stat['offense']['passingDowns']['explosiveness'],
                        'offense_rushing_plays_rate' => $stat['offense']['rushingPlays']['rate'],
                        'offense_rushing_plays_ppa' => $stat['offense']['rushingPlays']['ppa'],
                        'offense_rushing_plays_total_ppa' => $stat['offense']['rushingPlays']['totalPPA'],
                        'offense_rushing_plays_success_rate' => $stat['offense']['rushingPlays']['successRate'],
                        'offense_rushing_plays_explosiveness' => $stat['offense']['rushingPlays']['explosiveness'],
                        'offense_passing_plays_rate' => $stat['offense']['passingPlays']['rate'],
                        'offense_passing_plays_ppa' => $stat['offense']['passingPlays']['ppa'],
                        'offense_passing_plays_total_ppa' => $stat['offense']['passingPlays']['totalPPA'],
                        'offense_passing_plays_success_rate' => $stat['offense']['passingPlays']['successRate'],
                        'offense_passing_plays_explosiveness' => $stat['offense']['passingPlays']['explosiveness'],
                    ]
                );
            }

            $this->info('College football advanced season stats fetched and stored successfully.');
        } else {
            $this->error('Failed to fetch the data.');
        }
    }
}
