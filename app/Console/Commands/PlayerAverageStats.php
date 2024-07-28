<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NflPlayerStatsService;

class PlayerAverageStats extends Command
{
    protected $signature = 'player:average-stats {player_id}';
    protected $description = 'Calculate a player\'s average stats for different play periods of a game';

    protected NflPlayerStatsService $nflPlayerStatsService;

    public function __construct(NflPlayerStatsService $nflPlayerStatsService)
    {
        parent::__construct();
        $this->nflPlayerStatsService = $nflPlayerStatsService;
    }

    public function handle(): int
    {
        $playerId = $this->argument('player_id');
        $playPeriods = ['Q1', 'Q2', 'Q3', 'Q4', 'Q1&Q2', 'Q3&Q4'];
        $stats = [];

        foreach ($playPeriods as $period) {
            $stats[$period] = $this->nflPlayerStatsService->calculateAverageStats($playerId, $period);
        }

        $this->displayStats($stats);
        return 0;
    }

    protected function displayStats(array $stats): void
    {
        $headers = ['Period', 'Kick Yards', 'Receptions', 'Targets', 'Rec Yards', 'Pass Attempts', 'Pass Yards', 'Pass Completions', 'Rush Yards', 'Carries'];
        $data = [];

        foreach ($stats as $period => $stat) {
            $row = [$this->formatPeriod($period)];
            foreach (['kick_yards', 'receptions', 'targets', 'rec_yds', 'pass_attempts', 'pass_yds', 'pass_completions', 'rush_yds', 'carries'] as $key) {
                $row[] = round($stat[$key], 2) ?? 'N/A';
            }
            $data[] = $row;
        }

        $this->table($headers, $data);
    }

    protected function formatPeriod(string $period): string
    {
        switch ($period) {
            case 'Q1&Q2':
                return 'Half 1';
            case 'Q3&Q4':
                return 'Half 2';
            default:
                return $period;
        }
    }
}