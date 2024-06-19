<?php

namespace App\Traits;

use App\Models\NflPlayer;

trait StoresPlayerData
{
    protected function storePlayerData(array $player): void
    {
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
                'height' => $player['height'] ?? null,
                'weight' => $player['weight'] ?? null,
            ]
        );
    }
}
