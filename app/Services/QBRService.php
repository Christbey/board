<?php

namespace App\Services;

use App\Models\NflQbr;
use Carbon\Carbon;

class QBRService
{
    public function calculateQBR($attempts, $completions, $yards, $td, $int): float
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

    public function storeQBR($stat, $qbr, $playerId): void
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
}
