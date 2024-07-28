<?php

namespace App\Services;

use App\Models\NflInjury;
use App\Traits\StoresPlayerData;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RosterService
{
    use StoresPlayerData;

    public function storeRoster(array $roster, string $teamAbv): void
    {
        foreach ($roster as $player) {
            if (!is_array($player)) {
                Log::error('Invalid player data format for team: ' . $teamAbv);
                continue;
            }

            $this->storePlayerData($player);
            $this->storeInjuryData($player);
        }

        Log::info($teamAbv . ' roster has been saved successfully.');
    }

    protected function storeInjuryData(array $player): void
    {
        $injury = $player['injury'];

        if (empty($injury['description']) || empty($injury['designation'])) {
            return;
        }

        preg_match('/\(([^)]+)\)/', $injury['description'], $matches);
        $injuryType = $matches[1] ?? null;

        if ($injuryType === null) {
            return;
        }

        preg_match('/(\w+\s\d{1,2}):/', $injury['description'], $dateMatches);
        $injuryDate = isset($dateMatches[1]) ? Carbon::parse($dateMatches[1]) : null;

        if ($injuryDate === null) {
            return;
        }

        NflInjury::updateOrCreate(
            [
                'player_id' => $player['playerID'],
                'team_id' => $player['teamID']
            ],
            [
                'injury_type' => $injuryType,
                'injury_date' => $injuryDate,
                'designation' => $injury['designation'],
                'description' => $injury['description'],
            ]
        );
    }
}
