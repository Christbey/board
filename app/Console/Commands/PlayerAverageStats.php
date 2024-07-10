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

        if ($period === 'Q1&Q2') {
            $query->whereIn('play_period', ['Q1', 'Q2']);
        } elseif ($period === 'Q3&Q4') {
            $query->whereIn('play_period', ['Q3', 'Q4']);
        } else {
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

        $result = [];
        $gameCount = $totalStats->game_count ?: 1;

        foreach (['kick_yards', 'receptions', 'targets', 'rec_yds', 'pass_attempts', 'pass_yds', 'pass_completions', 'rush_yds', 'carries'] as $stat) {
            if ($totalStats->$stat !== null) {
                $result[$stat] = $totalStats->$stat / $gameCount;
            }
        }

        return $result;
    }

    protected function displayStats($stats): void
    {
        $headers = ['Qtr', 'K.Yds', 'Rec', 'Tar', 'Rec Yds', 'Pass Att', 'Pass Yds', 'Pass Comp', 'Rush Yds', 'Carries'];
        $data = [];

        foreach ($stats as $period => $stat) {
            $row = [$period === 'Q1&Q2' ? 'Half 1' : ($period === 'Q3&Q4' ? 'Half 2' : $period)];
            foreach (['kick_yards', 'receptions', 'targets', 'rec_yds', 'pass_attempts', 'pass_yds', 'pass_completions', 'rush_yds', 'carries'] as $key) {
                $row[] = $stat[$key] ?? 'N/A';
            }
            $data[] = $row;
        }

        $this->table($headers, $data);
    }
}
