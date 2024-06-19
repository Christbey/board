<?php

namespace App\Console\Commands\Nfl;

use Illuminate\Console\Command;
use App\Services\NFLStatsService;
use App\Traits\StoresPlayerData;

class GetNflPlayers extends Command
{
    use StoresPlayerData;  // Use the trait here

    protected $signature = 'nfl:get-players';
    protected $description = 'Get NFL Player List';
    protected NFLStatsService $nflStatsService;

    public function __construct(NFLStatsService $nflStatsService)
    {
        parent::__construct();
        $this->nflStatsService = $nflStatsService;
    }

    public function handle(): void
    {
        $this->info('Fetching NFL player list...');

        $response = $this->nflStatsService->getNFLPlayerList();
        $playerList = $response['body'] ?? [];

        if (empty($playerList)) {
            $this->error('No player data found.');
            return;
        }

        $this->savePlayerList($playerList);
    }

    protected function savePlayerList(array $playerList): void
    {
        foreach ($playerList as $player) {
            $this->storePlayerData($player);
        }
        $this->info('Player data has been saved.');
    }
}
