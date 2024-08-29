<?php

namespace App\Console\Commands\Nfl\Stat;

use App\Models\NflPlayer;
use App\Models\NflPlayerStat;
use App\Services\Nfl\Player\QBRService;
use Illuminate\Console\Command;

class CalculateQBR extends Command
{
    protected $signature = 'calculate:qbr';
    protected $description = 'Calculate QBR for QB players and store it in the nfl_qbr table';

    protected QBRService $qbrService;

    public function __construct(QBRService $qbrService)
    {
        parent::__construct();
        $this->qbrService = $qbrService;
    }

    public function handle(): void
    {
        $qbStats = NflPlayerStat::with('player:id,player_id') // Load player relationship with only necessary fields
        ->whereHas('player', function ($query) {
            $query->where('pos', 'QB');
        })
            ->chunk(1000, function ($qbStats) {
                $playerIds = $qbStats->pluck('player.player_id')->unique();
                $players = NflPlayer::whereIn('player_id', $playerIds)->pluck('id', 'player_id');

                foreach ($qbStats as $stat) {
                    if (!isset($players[$stat->player_id])) {
                        $this->error("Player with player_id {$stat->player_id} not found in nfl_players table.");
                        continue;
                    }

                    $qbr = $this->qbrService->calculateQBR(
                        $stat->pass_attempts,
                        $stat->pass_completions,
                        $stat->pass_yards,
                        $stat->pass_td,
                        $stat->pass_int
                    );

                    $this->qbrService->storeQBR($stat, $qbr, $players[$stat->player_id]);
                }
            });

        $this->info('QBR calculation completed and stored in nfl_qbr table.');
    }
}
