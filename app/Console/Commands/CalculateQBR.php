<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NflPlayerStat;
use App\Models\NflQbr;
use App\Models\NflPlayer;
use Carbon\Carbon;

class CalculateQBR extends Command
{
    protected $signature = 'calculate:qbr';
    protected $description = 'Calculate QBR for QB players and store it in the nfl_qbr table';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        // Fetch QB stats using Eloquent
        $qbStats = NflPlayerStat::whereHas('player', function ($query) {
            $query->where('pos', 'QB');
        })->get();

        foreach ($qbStats as $stat) {
            $qbr = $this->calculateQBR(
                $stat->pass_attempts,
                $stat->pass_completions,
                $stat->pass_yards,
                $stat->pass_td,
                $stat->pass_int
            );

            // Fetch the actual player ID from nfl_players table
            $player = NflPlayer::where('player_id', $stat->player_id)->first();

            if ($player) {
                // Store the QBR for each game played by the QB
                $this->storeQBR($stat, $qbr, $player->id);
            } else {
                // Log the missing player_id for further investigation
                $this->error("Player with player_id {$stat->player_id} not found in nfl_players table.");
            }
        }

        $this->info('QBR calculation completed and stored in nfl_qbr table.');
    }

    private function storeQBR($stat, $qbr, $playerId): void
    {
        NflQbr::updateOrCreate(
            ['player_id' => $playerId, 'game_id' => $stat->game_id],
            [
                'team_id' => $stat->team_id,
                'qbr' => $qbr,
                'attempts' => $stat->pass_attempts,
                'completions' => $stat->pass_completions,
                'passing_yards' => $stat->pass_yards,
                'passing_touchdowns' => $stat->pass_td,
                'interceptions' => $stat->pass_int,
                'updated_at' => Carbon::now(),
                'created_at' => Carbon::now(),
            ]
        );
    }

    private function calculateQBR($attempts, $completions, $yards, $td, $int)
    {
        if ($attempts == 0) {
            return 0;
        }

        $a = (($completions / $attempts) - 0.3) * 5;
        $b = (($yards / $attempts) - 3) * 0.25;
        $c = ($td / $attempts) * 20;
        $d = 2.375 - (($int / $attempts) * 25);

        $qbr = (($a + $b + $c + $d) / 6) * 100;

        return max(0, min($qbr, 158.3));
    }
}
