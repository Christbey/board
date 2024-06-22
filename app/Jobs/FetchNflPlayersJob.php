<?php

namespace App\Jobs;

use App\Services\NFLStatsService;
use App\Traits\StoresPlayerData;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchNflPlayersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, StoresPlayerData;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(NFLStatsService $nflStatsService)
    {
        $response = $nflStatsService->getNFLPlayerList();
        $playerList = $response['body'] ?? [];

        if (empty($playerList)) {
            \Log::error('No player data found.');
            return;
        }

        $this->savePlayerList($playerList);
    }

    protected function savePlayerList(array $playerList): void
    {
        foreach ($playerList as $player) {
            $this->storePlayerData($player);
        }
        \Log::info('Player data has been saved.');
    }
}
