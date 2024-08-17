<?php

namespace App\Traits\CollegeFootball;

use App\Models\CollegeFootballAdvGameStat;
use App\Models\CollegeFootballAdvSeasonStat;
use App\Models\CollegeFootballConference;
use App\Models\CollegeFootballTeam;

trait CollegeFootballAdvStatsTrait
{
    public function extractOffenseStats($offense)
    {
        return [
            'offense_plays' => $offense['plays'] ?? null,
            'offense_drives' => $offense['drives'] ?? null,
            'offense_ppa' => $offense['ppa'] ?? null,
            'offense_total_ppa' => $offense['totalPPA'] ?? null,
            'offense_success_rate' => $offense['successRate'] ?? null,
            'offense_explosiveness' => $offense['explosiveness'] ?? null,
            'offense_power_success' => $offense['powerSuccess'] ?? null,
            'offense_stuff_rate' => $offense['stuffRate'] ?? null,
            'offense_line_yards' => $offense['lineYards'] ?? null,
            'offense_line_yards_total' => $offense['lineYardsTotal'] ?? null,
            'offense_second_level_yards' => $offense['secondLevelYards'] ?? null,
            'offense_second_level_yards_total' => $offense['secondLevelYardsTotal'] ?? null,
            'offense_open_field_yards' => $offense['openFieldYards'] ?? null,
            'offense_open_field_yards_total' => $offense['openFieldYardsTotal'] ?? null,
            'offense_standard_downs_ppa' => $offense['standardDowns']['ppa'] ?? null,
            'offense_standard_downs_success_rate' => $offense['standardDowns']['successRate'] ?? null,
            'offense_standard_downs_explosiveness' => $offense['standardDowns']['explosiveness'] ?? null,
            'offense_passing_downs_ppa' => $offense['passingDowns']['ppa'] ?? null,
            'offense_passing_downs_success_rate' => $offense['passingDowns']['successRate'] ?? null,
            'offense_passing_downs_explosiveness' => $offense['passingDowns']['explosiveness'] ?? null,
            'offense_rushing_plays_ppa' => $offense['rushingPlays']['ppa'] ?? null,
            'offense_rushing_plays_total_ppa' => $offense['rushingPlays']['totalPPA'] ?? null,
            'offense_rushing_plays_success_rate' => $offense['rushingPlays']['successRate'] ?? null,
            'offense_rushing_plays_explosiveness' => $offense['rushingPlays']['explosiveness'] ?? null,
            'offense_passing_plays_ppa' => $offense['passingPlays']['ppa'] ?? null,
            'offense_passing_plays_total_ppa' => $offense['passingPlays']['totalPPA'] ?? null,
            'offense_passing_plays_success_rate' => $offense['passingPlays']['successRate'] ?? null,
            'offense_passing_plays_explosiveness' => $offense['passingPlays']['explosiveness'] ?? null,
            'offense_total_opportunities' => $offense['totalOpportunities'] ?? null,
        ];
    }

    public function extractDefenseStats($defense)
    {
        return [
            'defense_plays' => $defense['plays'] ?? null,
            'defense_drives' => $defense['drives'] ?? null,
            'defense_ppa' => $defense['ppa'] ?? null,
            'defense_total_ppa' => $defense['totalPPA'] ?? null,
            'defense_success_rate' => $defense['successRate'] ?? null,
            'defense_explosiveness' => $defense['explosiveness'] ?? null,
            'defense_power_success' => $defense['powerSuccess'] ?? null,
            'defense_stuff_rate' => $defense['stuffRate'] ?? null,
            'defense_line_yards' => $defense['lineYards'] ?? null,
            'defense_line_yards_total' => $defense['lineYardsTotal'] ?? null,
            'defense_second_level_yards' => $defense['secondLevelYards'] ?? null,
            'defense_second_level_yards_total' => $defense['secondLevelYardsTotal'] ?? null,
            'defense_open_field_yards' => $defense['openFieldYards'] ?? null,
            'defense_open_field_yards_total' => $defense['openFieldYardsTotal'] ?? null,
            'defense_standard_downs_ppa' => $defense['standardDowns']['ppa'] ?? null,
            'defense_standard_downs_success_rate' => $defense['standardDowns']['successRate'] ?? null,
            'defense_standard_downs_explosiveness' => $defense['standardDowns']['explosiveness'] ?? null,
            'defense_passing_downs_ppa' => $defense['passingDowns']['ppa'] ?? null,
            'defense_passing_downs_success_rate' => $defense['passingDowns']['successRate'] ?? null,
            'defense_passing_downs_explosiveness' => $defense['passingDowns']['explosiveness'] ?? null,
            'defense_rushing_plays_ppa' => $defense['rushingPlays']['ppa'] ?? null,
            'defense_rushing_plays_total_ppa' => $defense['rushingPlays']['totalPPA'] ?? null,
            'defense_rushing_plays_success_rate' => $defense['rushingPlays']['successRate'] ?? null,
            'defense_rushing_plays_explosiveness' => $defense['rushingPlays']['explosiveness'] ?? null,
            'defense_passing_plays_ppa' => $defense['passingPlays']['ppa'] ?? null,
            'defense_passing_plays_total_ppa' => $defense['passingPlays']['totalPPA'] ?? null,
            'defense_passing_plays_success_rate' => $defense['passingPlays']['successRate'] ?? null,
            'defense_passing_plays_explosiveness' => $defense['passingPlays']['explosiveness'] ?? null,
        ];
    }

    public function processSeasonStats(array $stat)
    {
        $team = CollegeFootballTeam::firstOrCreate(
            ['school' => $stat['team']],
            ['school' => $stat['team']]
        );

        $conference = null;
        if (isset($stat['conference'])) {
            $conference = CollegeFootballConference::firstOrCreate(
                ['abbreviation' => $stat['conference']],
                ['abbreviation' => $stat['conference']]
            );
        }

        CollegeFootballAdvSeasonStat::updateOrCreate(
            [
                'season' => $stat['season'],
                'team_id' => $team->id,
            ],
            array_merge(
                ['conference_id' => $conference->id ?? null],
                $this->extractOffenseStats($stat['offense']),
                $this->extractDefenseStats($stat['defense']),
                [

                ]
            )
        );
    }

    public function processGameStats(array $stat)
    {
        $team = CollegeFootballTeam::firstOrCreate(
            ['school' => $stat['team']],
            ['school' => $stat['team']]
        );

        $opponent = CollegeFootballTeam::firstOrCreate(
            ['school' => $stat['opponent']],
            ['school' => $stat['opponent']]
        );

        $conference = null;
        if (isset($stat['conference'])) {
            $conference = CollegeFootballConference::firstOrCreate(
                ['abbreviation' => $stat['conference']],
                ['abbreviation' => $stat['conference']]
            );
        }

        CollegeFootballAdvGameStat::updateOrCreate(
            [
                'game_id' => $stat['gameId'],
                'team_id' => $team->id,
            ],
            array_merge(
                [
                    'season' => $stat['season'],
                    'week' => $stat['week'],
                    'opponent_id' => $opponent->id,
                    'conference_id' => $conference->id ?? null,
                ],
                $this->extractOffenseStats($stat['offense']),
                $this->extractDefenseStats($stat['defense'])
            )
        );
    }

    public function reverseStat($stat)
    {
        return [
            'gameId' => $stat['gameId'],
            'week' => $stat['week'],
            'team' => $stat['opponent'],
            'opponent' => $stat['team'],
            'offense' => $stat['defense'],
            'defense' => $stat['offense'],
        ];
    }
}
