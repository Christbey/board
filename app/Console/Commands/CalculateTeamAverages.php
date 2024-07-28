<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TeamAverageService;
use App\Helpers\NflHelper;

class CalculateTeamAverages extends Command
{
    protected $signature = 'calculate:team-averages {team_id1?} {team_id2?} {year?}';
    protected $description = 'Calculate average points for NFL teams based on given team IDs and optional year';

    protected TeamAverageService $teamAverageService;

    public function __construct(TeamAverageService $teamAverageService)
    {
        parent::__construct();
        $this->teamAverageService = $teamAverageService;
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
        $team1HomeAvg = $this->teamAverageService->getAveragePointsForMatchup($teamId1, $teamId2, 'home', $seasonRange);
        $team1AwayAvg = $this->teamAverageService->getAveragePointsForMatchup($teamId1, $teamId2, 'away', $seasonRange);
        $team2HomeAvg = $this->teamAverageService->getAveragePointsForMatchup($teamId2, $teamId1, 'home', $seasonRange);
        $team2AwayAvg = $this->teamAverageService->getAveragePointsForMatchup($teamId2, $teamId1, 'away', $seasonRange);

        $this->displayAverage("Team $teamId1 Home Avg Against Team $teamId2: $team1HomeAvg", $team1HomeAvg);
        $this->displayAverage("Team $teamId1 Away Avg Against Team $teamId2: $team1AwayAvg", $team1AwayAvg);
        $this->displayAverage("Team $teamId2 Home Avg Against Team $teamId1: $team2HomeAvg", $team2HomeAvg);
        $this->displayAverage("Team $teamId2 Away Avg Against Team $teamId1: $team2AwayAvg", $team2AwayAvg);
    }

    private function calculateSingleTeamAverages($teamId, $seasonRange): void
    {
        $homeAvg = $this->teamAverageService->getAveragePoints($teamId, 'home', $seasonRange);
        $awayAvg = $this->teamAverageService->getAveragePoints($teamId, 'away', $seasonRange);

        $this->displayAverage("Home Avg: $homeAvg", $homeAvg);
        $this->displayAverage("Away Avg: $awayAvg", $awayAvg);
    }

    private function calculateAllTeamsAverages($seasonRange): void
    {
        $teams = $this->teamAverageService->getTeamIds();

        foreach ($teams as $teamId) {
            $homeAvg = $this->teamAverageService->getAveragePoints($teamId, 'home', $seasonRange);
            $awayAvg = $this->teamAverageService->getAveragePoints($teamId, 'away', $seasonRange);

            if ($homeAvg > 0 || $awayAvg > 0) {
                $this->info("Team ID: $teamId -> Home Avg: $homeAvg, Away Avg: $awayAvg");
            }
        }
    }

    private function displayAverage(string $message, float $average): void
    {
        if ($average > 0) {
            $this->info($message);
        }
    }
}
