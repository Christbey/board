<?php

namespace App\Traits;

trait FormatsPlayerStats
{
    protected function formatPlayerStats(array $player): array
    {
        return [
            'team_id' => $player['teamID'] ?? null,
            'team_abv' => $player['teamAbv'] ?? null,
            'player_name' => $player['longName'] ?? null,
            'rush_yards' => $player['Rushing']['rushYds'] ?? null,
            'carries' => $player['Rushing']['carries'] ?? null,
            'rush_td' => $player['Rushing']['rushTD'] ?? null,
            'receptions' => $player['Receiving']['receptions'] ?? null,
            'rec_td' => $player['Receiving']['recTD'] ?? null,
            'targets' => $player['Receiving']['targets'] ?? null,
            'rec_yards' => $player['Receiving']['recYds'] ?? null,
            'games_played' => 1,
            'total_tackles' => $player['Defense']['totalTackles'] ?? null,
            'fumbles_lost' => $player['Fumbles']['fumblesLost'] ?? null,
            'def_td' => $player['Defense']['defTD'] ?? null,
            'fumbles' => $player['Fumbles']['fumbles'] ?? null,
            'fumbles_recovered' => $player['Defense']['fumblesRecovered'] ?? null,
            'solo_tackles' => $player['Defense']['soloTackles'] ?? null,
            'defensive_interceptions' => $player['Defense']['defensiveInterceptions'] ?? null,
            'qb_hits' => $player['Defense']['qbHits'] ?? null,
            'tfl' => $player['Defense']['tfl'] ?? null,
            'pass_deflections' => $player['Defense']['passDeflections'] ?? null,
            'sacks' => $player['Defense']['sacks'] ?? null,
            'pass_yards' => $player['Passing']['passYds'] ?? null,
            'pass_int' => $player['Passing']['int'] ?? null,
            'pass_td' => $player['Passing']['passTD'] ?? null,
            'pass_rtg' => $this->validateDecimal($player['Passing']['rtg'] ?? null),
            'pass_qbr' => $this->validateDecimal($player['Passing']['qbr'] ?? null),
            'pass_completions' => $player['Passing']['passCompletions'] ?? null,
            'pass_attempts' => $player['Passing']['passAttempts'] ?? null,
            'sacked' => $player['Passing']['sacked'] ?? null,
            'pass_avg' => $this->validateDecimal($player['Passing']['passAvg'] ?? null),
        ];
    }

    private function validateDecimal(mixed $value): ?float
    {
        return is_numeric($value) ? (float) $value : null;
    }
}
