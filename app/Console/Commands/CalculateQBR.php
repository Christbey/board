<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NflPlayerStat;
use App\Models\NflPlayer;
use App\Services\QBRService;
use Illuminate\Support\Facades\Log;

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
        $qbStats = NflPlayerStat::whereHas('player', function ($query) {
            $query->where('pos', 'QB');
        })->get();

        foreach ($qbStats as $stat) {
            $qbr = $this->qbrService->calculateQBR(
                $stat->pass_attempts,
                $stat->pass_completions,
                $stat->pass_yards,
                $stat->pass_td,
                $stat->pass_int
            );

            $player = NflPlayer::where('player_id', $stat->player_id)->first();

            if ($player) {
                $this->qbrService->storeQBR($stat, $qbr, $player->id);
            } else {
                $this->error("Player with player_id {$stat->player_id} not found in nfl_players table.");
            }
        }

        $this->info('QBR calculation completed and stored in nfl_qbr table.');
    }
}
