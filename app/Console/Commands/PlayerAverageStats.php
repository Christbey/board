<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NflPlayByPlay;
use Illuminate\Support\Facades\DB;

class PlayerAverageStats extends Command
{
    protected $signature = 'player:average-stats {player_id}';
    protected $description = 'Calculate a player\'s average stats for different play periods of a game';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $playerId = $this->argument('player_id');
        $playPeriods = ['Q1', 'Q2', 'Q3', 'Q4', 'Q1&Q2', 'Q3&Q4'];
        $stats = [];

        foreach ($playPeriods as $period) {
            $stats[$period] = $this->calculateAverageStats($playerId, $period);
        }

        $this->displayStats($stats);
        return 0;
    }

    protected function calculateAverageStats($playerId, $period): array
    {
        $query = NflPlayByPlay::where('player_id', $playerId);

        switch ($period) {
            case 'Q1&Q2':
                $query->whereIn('play_period', ['Q1', 'Q2']);
                break;
            case 'Q3&Q4':
                $query->whereIn('play_period', ['Q3', 'Q4']);
                break;
            default:
                $query->where('play_period', $period);
        }

        $totalStats = $query->select(
            DB::raw('SUM(kick_yards) as kick_yards'),
            DB::raw('SUM(receptions) as receptions'),
            DB::raw('SUM(targets) as targets'),
            DB::raw('SUM(rec_yds) as rec_yds'),
            DB::raw('SUM(pass_attempts) as pass_attempts'),
            DB::raw('SUM(pass_yds) as pass_yds'),
            DB::raw('SUM(pass_completions) as pass_completions'),
            DB::raw('SUM(rush_yds) as rush_yds'),
            DB::raw('SUM(carries) as carries'),
            DB::raw('COUNT(DISTINCT game_id) as game_count')
        )->first();

        $gameCount = max($totalStats->game_count, 1); // Ensure we don't divide by zero
        $stats = [
            'kick_yards' => $totalStats->kick_yards / $gameCount,
            'receptions' => $totalStats->receptions / $gameCount,
            'targets' => $totalStats->targets / $gameCount,
            'rec_yds' => $totalStats->rec_yds / $gameCount,
            'pass_attempts' => $totalStats->pass_attempts / $gameCount,
            'pass_yds' => $totalStats->pass_yds / $gameCount,
            'pass_completions' => $totalStats->pass_completions / $gameCount,
            'rush_yds' => $totalStats->rush_yds / $gameCount,
            'carries' => $totalStats->carries / $gameCount,
        ];

        return $stats;
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
