<?php

namespace App\Console\Commands;

use App\Models\NflPlayer;
use Illuminate\Console\Command;
use App\Services\NFLStatsService;
use App\Models\Player;

class GetNFLPlayerList extends Command
{
    protected $signature = 'nfl:get-player-list';
    protected $description = 'Get NFL Player List';
    protected $nflStatsService;

    public function __construct(NFLStatsService $nflStatsService)
    {
        parent::__construct();
        $this->nflStatsService = $nflStatsService;
    }

    public function handle()
    {
        $this->info('Fetching NFL player list...');

        $response = $this->nflStatsService->getNFLPlayerList();
        $playerList = $response['body'] ?? [];

        if (empty($playerList)) {
            $this->error('No player data found.');
            return;
        }

        // Save the player list data into the database
        $this->savePlayerList($playerList);
    }

    protected function savePlayerList(array $playerList)
    {
        foreach ($playerList as $player) {
            NflPlayer::updateOrCreate(
                ['player_id' => $player['playerID'] ?? null],
                [
                    'longName' => $player['longName'] ?? null,
                    'team' => $player['team'] ?? null,
                    'jerseyNum' => $player['jerseyNum'] ?? null,
                    'pos' => $player['pos'] ?? null,
                    'exp' => $player['exp'] ?? null,
                    'school' => $player['school'] ?? null,
                    'age' => isset($player['age']) ? (int)$player['age'] : null,
                ]
            );
        }

        $this->info('Player data has been saved.');
    }
}
