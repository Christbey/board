<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NFLStatsService;

class GetNFLTeams extends Command
{
    protected $signature = 'nfl:get-teams {--schedules} {--rosters} {--topPerformers} {--teamStats}';
    protected $description = 'Get NFL teams data with optional details';
    protected NFLStatsService $nflStatsService;

    public function __construct(NFLStatsService $nflStatsService)
    {
        parent::__construct();
        $this->nflStatsService = $nflStatsService;
    }

    public function handle(): void
    {
        $schedules = $this->option('schedules');
        $rosters = $this->option('rosters');
        $topPerformers = $this->option('topPerformers');
        $teamStats = $this->option('teamStats');

        $response = $this->nflStatsService->getNFLTeams($schedules, $rosters, $topPerformers, $teamStats);
        $teams = $response['body'] ?? [];

        if (empty($teams)) {
            $this->error('No teams found.');
            return;
        }

        foreach ($teams as $team) {
            if (is_array($team)) {
                $this->displayTeamInfo($team);
            } else {
                $this->error('Invalid data format received.');
            }
        }
    }

    protected function displayTeamInfo(array $team)
    {
        $this->info('Team: ' . ($team['teamName'] ?? 'N/A'));
        $this->info('City: ' . ($team['teamCity'] ?? 'N/A'));
        $this->info('Wins: ' . ($team['wins'] ?? 'N/A'));
        $this->info('Losses: ' . ($team['loss'] ?? 'N/A'));
        $this->info('Division: ' . ($team['division'] ?? 'N/A'));
        $this->info('Conference: ' . ($team['conference'] ?? 'N/A'));

        if (isset($team['teamStats']) && is_array($team['teamStats'])) {
            $this->info('Team Stats: ' . json_encode($team['teamStats']));
        }

        $this->info('---');
    }
}
