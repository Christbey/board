<?php

namespace App\Traits;

use App\Models\CollegeFootballConference;
use App\Models\CollegeFootballGame;
use App\Models\CollegeFootballTeam;

trait UpdatesCollegeFootballGames
{
    /**
     * Update or create a CollegeFootballGame record.
     *
     * @param array $game
     * @param CollegeFootballTeam $homeTeam
     * @param CollegeFootballTeam $awayTeam
     * @param CollegeFootballConference|null $homeConference
     * @param CollegeFootballConference|null $awayConference
     * @return CollegeFootballGame
     */
    protected function updateOrCreateCollegeFootballGame(array $game, $homeTeam, $awayTeam, $homeConference = null, $awayConference = null)
    {
        return CollegeFootballGame::updateOrCreate(
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
                'home_team_id' => $homeTeam->id ?? null,
                'home_conference_id' => $homeConference->id ?? null,
                'away_id' => $game['away_id'],
                'away_team' => $game['away_team'],
                'away_conference' => $game['away_conference'] ?? null,
                'away_division' => $game['away_division'] ?? null,
                'away_points' => $game['away_points'] ?? null,
                'away_line_scores' => $game['away_line_scores'] ?? null,
                'away_post_win_prob' => $game['away_post_win_prob'] ?? null,
                'away_pregame_elo' => $game['away_pregame_elo'] ?? null,
                'away_postgame_elo' => $game['away_postgame_elo'] ?? null,
                'away_team_id' => $awayTeam->id ?? null,
                'away_conference_id' => $awayConference->id ?? null,
                'excitement_index' => $game['excitement_index'] ?? null,
                'highlights' => $game['highlights'] ?? null,
                'notes' => $game['notes'] ?? null,
            ]
        );
    }
}
