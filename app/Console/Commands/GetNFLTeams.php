<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NFLStatsService;

class GetNFLTeams extends Command
{
    protected $signature = 'nfl:get-teams {--schedules} {--rosters} {--topPerformers} {--teamStats}';
    protected $description = 'Get NFL teams data with optional details';
    protected $nflStatsService;

    public function __construct(NFLStatsService $nflStatsService)
    {
        parent::__construct();
        $this->nflStatsService = $nflStatsService;
    }

    public function handle()
    {
        $schedules = $this->option('schedules');
        $rosters = $this->option('rosters');
        $topPerformers = $this->option('topPerformers');
        $teamStats = $this->option('teamStats');

        // Debugging output
        $this->info('Options received:');
        $this->info('schedules: ' . var_export($schedules, true));
        $this->info('rosters: ' . var_export($rosters, true));
        $this->info('topPerformers: ' . var_export($topPerformers, true));
        $this->info('teamStats: ' . var_export($teamStats, true));

        $response = $this->nflStatsService->getNFLTeams($schedules, $rosters, $topPerformers, $teamStats);
        $teams = $response['body'] ?? [];

        if (empty($teams)) {
            $this->error('No teams found.');
            return;
        }

        // Debugging output to check the response structure
        \Log::info('API Response: ' . json_encode($teams));

        foreach ($teams as $team) {
            if (is_array($team)) {
                $this->info('Team: ' . ($team['teamName'] ?? 'N/A'));
                $this->info('City: ' . ($team['teamCity'] ?? 'N/A'));
                $this->info('Wins: ' . ($team['wins'] ?? 'N/A'));
                $this->info('Losses: ' . ($team['loss'] ?? 'N/A'));
                $this->info('Division: ' . ($team['division'] ?? 'N/A'));
                $this->info('Conference: ' . ($team['conference'] ?? 'N/A'));

                // Check if teamStats exist
                if (isset($team['teamStats']) && is_array($team['teamStats'])) {
                    $this->info('Team Stats: ' . json_encode($team['teamStats']));
                }

                $this->info('---');
            } else {
                $this->error('Invalid data format received.');
            }
        }
    }
}
