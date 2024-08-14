<?php

namespace App\Traits;

use App\Models\CollegeFootballTeam;
use App\Models\CollegeFootballConference;
use App\Models\CollegeFootballAdvSeasonStat;

trait CollegeFootballAdvSeasonTrait
{
    public function processStats(array $stat)
    {
        // Find or create the team
        $team = CollegeFootballTeam::firstOrCreate(
            ['school' => $stat['team']],
            ['school' => $stat['team']]
        );

        // Find or create the conference
        $conference = null;
        if (isset($stat['conference'])) {
            $conference = CollegeFootballConference::firstOrCreate(
                ['abbreviation' => $stat['conference']],
                ['abbreviation' => $stat['conference']]
            );
        }

        // Update or create the advanced season stats
        CollegeFootballAdvSeasonStat::updateOrCreate(
            [
                'season' => $stat['season'],
                'team_id' => $team->id,
            ],
            [
                'conference_id' => $conference->id ?? null,

                // Offense Stats
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

                // Defense Stats
                'defense_plays' => $stat['defense']['plays'],
                'defense_drives' => $stat['defense']['drives'],
                'defense_ppa' => $stat['defense']['ppa'],
                'defense_total_ppa' => $stat['defense']['totalPPA'],
                'defense_success_rate' => $stat['defense']['successRate'],
                'defense_explosiveness' => $stat['defense']['explosiveness'],
                'defense_power_success' => $stat['defense']['powerSuccess'],
                'defense_stuff_rate' => $stat['defense']['stuffRate'],
                'defense_line_yards' => $stat['defense']['lineYards'],
                'defense_line_yards_total' => $stat['defense']['lineYardsTotal'],
                'defense_second_level_yards' => $stat['defense']['secondLevelYards'],
                'defense_second_level_yards_total' => $stat['defense']['secondLevelYardsTotal'],
                'defense_open_field_yards' => $stat['defense']['openFieldYards'],
                'defense_open_field_yards_total' => $stat['defense']['openFieldYardsTotal'],
                'defense_total_opportunities' => $stat['defense']['totalOpportunies'],
                'defense_points_per_opportunity' => $stat['defense']['pointsPerOpportunity'],
                'defense_field_position_average_start' => $stat['defense']['fieldPosition']['averageStart'],
                'defense_field_position_average_predicted_points' => $stat['defense']['fieldPosition']['averagePredictedPoints'],
                'defense_havoc_total' => $stat['defense']['havoc']['total'],
                'defense_havoc_front_seven' => $stat['defense']['havoc']['frontSeven'],
                'defense_havoc_db' => $stat['defense']['havoc']['db'],
                'defense_standard_downs_rate' => $stat['defense']['standardDowns']['rate'],
                'defense_standard_downs_ppa' => $stat['defense']['standardDowns']['ppa'],
                'defense_standard_downs_success_rate' => $stat['defense']['standardDowns']['successRate'],
                'defense_standard_downs_explosiveness' => $stat['defense']['standardDowns']['explosiveness'],
                'defense_passing_downs_rate' => $stat['defense']['passingDowns']['rate'],
                'defense_passing_downs_ppa' => $stat['defense']['passingDowns']['ppa'],
                'defense_passing_downs_success_rate' => $stat['defense']['passingDowns']['successRate'],
                'defense_passing_downs_explosiveness' => $stat['defense']['passingDowns']['explosiveness'],
                'defense_rushing_plays_rate' => $stat['defense']['rushingPlays']['rate'],
                'defense_rushing_plays_ppa' => $stat['defense']['rushingPlays']['ppa'],
                'defense_rushing_plays_total_ppa' => $stat['defense']['rushingPlays']['totalPPA'],
                'defense_rushing_plays_success_rate' => $stat['defense']['rushingPlays']['successRate'],
                'defense_rushing_plays_explosiveness' => $stat['defense']['rushingPlays']['explosiveness'],
                'defense_passing_plays_rate' => $stat['defense']['passingPlays']['rate'],
                'defense_passing_plays_ppa' => $stat['defense']['passingPlays']['ppa'],
                'defense_passing_plays_total_ppa' => $stat['defense']['passingPlays']['totalPPA'],
                'defense_passing_plays_success_rate' => $stat['defense']['passingPlays']['successRate'],
                'defense_passing_plays_explosiveness' => $stat['defense']['passingPlays']['explosiveness'],
            ]
        );
    }
}
