<?php

namespace App\Traits\CollegeFootball;

use App\Models\CollegeFootballGamePpa;
use App\Models\CollegeFootballPpa;

trait CollegeFootballPpaTrait
{
    public function storeGamePpaData($gameId, $teamId, $opponentId, $conferenceId, array $game)
    {
        CollegeFootballGamePpa::updateOrCreate(
            ['game_id' => $gameId],
            [
                'season' => $game['season'],
                'week' => $game['week'],
                'team_id' => $teamId,
                'conference_id' => $conferenceId,
                'opponent_id' => $opponentId,
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

    public function storeTeamPpaData($teamId, array $teamData)
    {
        CollegeFootballPpa::updateOrCreate(
            [
                'team_id' => $teamId,
                'season' => $teamData['season'],
            ],
            [
                'conference' => $teamData['conference'] ?? null,
                'offense_overall' => $teamData['offense']['overall'] ?? null,
                'offense_passing' => $teamData['offense']['passing'] ?? null,
                'offense_rushing' => $teamData['offense']['rushing'] ?? null,
                'offense_first_down' => $teamData['offense']['firstDown'] ?? null,
                'offense_second_down' => $teamData['offense']['secondDown'] ?? null,
                'offense_third_down' => $teamData['offense']['thirdDown'] ?? null,
                'defense_overall' => $teamData['defense']['overall'] ?? null,
                'defense_passing' => $teamData['defense']['passing'] ?? null,
                'defense_rushing' => $teamData['defense']['rushing'] ?? null,
                'defense_first_down' => $teamData['defense']['firstDown'] ?? null,
                'defense_second_down' => $teamData['defense']['secondDown'] ?? null,
                'defense_third_down' => $teamData['defense']['thirdDown'] ?? null,
            ]
        );
    }
}
