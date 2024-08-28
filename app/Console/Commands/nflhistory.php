<?php

namespace App\Console\Commands;

use App\Models\NflOdds;
use App\Models\NflTeam;
use DB;
use Illuminate\Console\Command;
use App\Models\EspnNflPastH2h;
use Log;

class NflHistory extends Command
{
    protected $signature = 'nfl:history';
    protected $description = 'Fetch NFL past head-to-head data';

    public function handle()
    {
        $this->nflHistory();
    }

    public function nflHistory()
    {
        $teamId = 12; // Replace with your team ID
        $spread = 1.5; // Replace with your spread value

        $results = EspnNflPastH2h::where('spread', $spread)
            ->where(function ($query) use ($teamId) {
                $query->where('home_team_id', $teamId)->orWhere('away_team_id', $teamId);
            })
            ->selectRaw(
                '
        SUM(CASE WHEN (home_team_id = ? AND home_team_money_line_winner = 1) OR (away_team_id = ? AND away_team_money_line_winner = 1) THEN 1 ELSE 0 END) as wins,
        SUM(CASE WHEN (home_team_id = ? AND home_team_money_line_winner = 0) OR (away_team_id = ? AND away_team_money_line_winner = 0) THEN 1 ELSE 0 END) as losses
    ',
                [$teamId, $teamId, $teamId, $teamId]
            )
            ->first();

        $wins = $results->wins;
        $losses = $results->losses;

        echo "The team with ID $teamId won $wins times and lost $losses times at the spread of $spread.";

    }

    public function oddsHistoryPresent()
    {
        $teamId = 3; // Replace with your team ID
        $spread = 3.0; // Replace with your spread value

        // Step 1: Fetch the espn_team_id and team name for the provided teamId
        $team = NflTeam::where('id', $teamId)->first(['espn_team_id', 'name']);

        if (!$team) {
            echo "No team found for ID $teamId.";
            return;
        }

        $espnTeamId = $team->espn_team_id;
        $teamName = $team->name;

        // Step 2: Count occurrences in the nfl_odds table using the provided team ID
        $oddsCount = NflOdds::where(function ($query) use ($teamId) {
            $query->where('home_team_id', $teamId)->orWhere('away_team_id', $teamId);
        })
            ->where(function ($query) use ($spread) {
                $query
                    ->where('spread_home_point', $spread)
                    ->orWhere('spread_away_point', $spread);
            })
            ->count();

        // Step 3: Calculate wins and losses in the EspnNflPastH2h table using the espn_team_id
        $results = EspnNflPastH2h::where('spread', $spread)
            ->where(function ($query) use ($espnTeamId) {
                $query
                    ->where('home_team_id', $espnTeamId)
                    ->orWhere('away_team_id', $espnTeamId);
            })
            ->selectRaw(
                '
        SUM(CASE WHEN (home_team_id = ? AND home_team_money_line_winner = 1) OR (away_team_id = ? AND away_team_money_line_winner = 1) THEN 1 ELSE 0 END) as wins,
        SUM(CASE WHEN (home_team_id = ? AND home_team_money_line_winner = 0) OR (away_team_id = ? AND away_team_money_line_winner = 0) THEN 1 ELSE 0 END) as losses
        ',
                [$espnTeamId, $espnTeamId, $espnTeamId, $espnTeamId]
            )
            ->first();

        if ($results) {
            $wins = $results->wins;
            $losses = $results->losses;

            // Output combined results with team name
            echo "The spread of $spread occurred $oddsCount times for the '$teamName'";

            echo "\n\n";

            echo " The '$teamName' won $wins times and lost $losses times at the spread of $spread.";
        } else {
            echo 'No data found for the given conditions in the EspnNflPastH2h table.';
        }

    }


}