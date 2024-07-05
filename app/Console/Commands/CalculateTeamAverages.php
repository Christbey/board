<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NflTeamSchedule;
use App\Models\NflTeam;
use App\Helpers\NflHelper;

class CalculateTeamAverages extends Command
{
    protected $signature = 'calculate:team-averages {team_id1?} {team_id2?} {year?}';
    protected $description = 'Calculate average points for NFL teams based on given team IDs and optional year';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $teamId1 = $this->argument('team_id1');
        $teamId2 = $this->argument('team_id2');
        $year = $this->argument('year');
        $seasonRange = $year ? NflHelper::getSeasonDateRange($year) : null;

        if ($teamId1 && $teamId2) {
            $this->info("Calculating averages for games between Team ID: $teamId1 and Team ID: $teamId2");
            $this->calculateMatchupAverages($teamId1, $teamId2, $seasonRange);
        } elseif ($teamId1) {
            $this->info("Calculating averages for Team ID: $teamId1");
            $this->calculateSingleTeamAverages($teamId1, $seasonRange);
        } else {
            $this->info('Calculating averages for all teams');
            $this->calculateAllTeamsAverages($seasonRange);
        }
    }

    private function calculateMatchupAverages($teamId1, $teamId2, $seasonRange): void
    {
        $team1HomeAvg = $this->getAveragePointsForMatchup($teamId1, $teamId2, 'home', $seasonRange);
        $team1AwayAvg = $this->getAveragePointsForMatchup($teamId1, $teamId2, 'away', $seasonRange);
        $team2HomeAvg = $this->getAveragePointsForMatchup($teamId2, $teamId1, 'home', $seasonRange);
        $team2AwayAvg = $this->getAveragePointsForMatchup($teamId2, $teamId1, 'away', $seasonRange);

        if ($team1HomeAvg > 0) {
            $this->info("Team $teamId1 Home Avg Against Team $teamId2: $team1HomeAvg");
        }
        if ($team1AwayAvg > 0) {
            $this->info("Team $teamId1 Away Avg Against Team $teamId2: $team1AwayAvg");
        }
        if ($team2HomeAvg > 0) {
            $this->info("Team $teamId2 Home Avg Against Team $teamId1: $team2HomeAvg");
        }
        if ($team2AwayAvg > 0) {
            $this->info("Team $teamId2 Away Avg Against Team $teamId1: $team2AwayAvg");
        }
    }

    private function calculateSingleTeamAverages($teamId, $seasonRange): void
    {
        $homeAvg = $this->getAveragePoints($teamId, 'home', $seasonRange);
        $awayAvg = $this->getAveragePoints($teamId, 'away', $seasonRange);

        if ($homeAvg > 0) {
            $this->info("Home Avg: $homeAvg");
        }
        if ($awayAvg > 0) {
            $this->info("Away Avg: $awayAvg");
        }
    }

    private function calculateAllTeamsAverages($seasonRange): void
    {
        $teams = NflTeam::pluck('id');

        foreach ($teams as $teamId) {
            $homeAvg = $this->getAveragePoints($teamId, 'home', $seasonRange);
            $awayAvg = $this->getAveragePoints($teamId, 'away', $seasonRange);

            if ($homeAvg > 0 || $awayAvg > 0) {
                $this->info("Team ID: $teamId -> Home Avg: $homeAvg, Away Avg: $awayAvg");
            }
        }
    }

    private function getAveragePointsForMatchup($teamId1, $teamId2, $type, $seasonRange): float
    {
        $oppositeType = $type === 'home' ? 'away' : 'home';

        $query = NflTeamSchedule::where("team_id_{$type}", $teamId1)
            ->where("team_id_{$oppositeType}", $teamId2)
            ->whereNotNull("{$type}_pts")
            ->where('game_status', 'Completed');

        if ($seasonRange) {
            $query->whereBetween('game_date', $seasonRange);
        }

        $totalPoints = $query->sum("{$type}_pts");
        $totalGames = $query->count();

        return $totalGames > 0 ? (float)$totalPoints / $totalGames : 0;
    }

    private function getAveragePoints($teamId, $type, $seasonRange): float
    {
        $query = NflTeamSchedule::where("team_id_{$type}", $teamId)
            ->whereNotNull("{$type}_pts")
            ->where('game_status', 'Completed');

        if ($seasonRange) {
            $query->whereBetween('game_date', $seasonRange);
        }

        $totalPoints = $query->sum("{$type}_pts");
        $totalGames = $query->count();

        return $totalGames > 0 ? (float)$totalPoints / $totalGames : 0;
    }
}
