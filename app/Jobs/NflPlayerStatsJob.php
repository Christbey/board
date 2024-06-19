<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\NFLTeam;
use App\Models\NflPlayerStat;
use Illuminate\Support\Facades\Log;

class NflPlayerStatsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $gameID;
    protected $playerStats;

    public function __construct($gameID, $playerStats)
    {
        $this->gameID = $gameID;
        $this->playerStats = $playerStats;
    }

    public function handle()
    {
        foreach ($this->playerStats as $playerID => $stats) {
            $teamAbv = $stats['teamAbv'] ?? null;
            $team = NFLTeam::where('abbreviation', $teamAbv)->first();

            if (!$team) {
                Log::error('Team not found for abbreviation: ' . $teamAbv);
                continue;
            }

            $statData = $this->extractStats($stats);

            NflPlayerStat::updateOrCreate(
                ['game_id' => $this->gameID, 'player_id' => $playerID],
                array_merge(
                    ['team_id' => $team->id, 'team_abv' => $teamAbv, 'player_name' => $stats['longName']],
                    $statData
                )
            );

            Log::info('Player stats stored for player: ' . $stats['longName']);
        }
    }

    // Include your extractStats function here or call a service class method
    protected function extractStats(array $stats): array
    {
        return [
            'rush_yards' => $this->parseInt($stats['Rushing']['rushYds'] ?? 0),
            'carries' => $this->parseInt($stats['Rushing']['carries'] ?? 0),
            'rush_td' => $this->parseInt($stats['Rushing']['rushTD'] ?? 0),
            'receptions' => $this->parseInt($stats['Receiving']['receptions'] ?? 0),
            'rec_td' => $this->parseInt($stats['Receiving']['recTD'] ?? 0),
            'targets' => $this->parseInt($stats['Receiving']['targets'] ?? 0),
            'rec_yards' => $this->parseInt($stats['Receiving']['recYds'] ?? 0),
            'games_played' => $this->parseInt($stats['Receiving']['gamesPlayed'] ?? 0),
            'total_tackles' => $this->parseInt($stats['Defense']['totalTackles'] ?? 0),
            'fumbles_lost' => $this->parseInt($stats['Defense']['fumblesLost'] ?? 0),
            'def_td' => $this->parseInt($stats['Defense']['defTD'] ?? 0),
            'fumbles' => $this->parseInt($stats['Defense']['fumbles'] ?? 0),
            'fumbles_recovered' => $this->parseInt($stats['Defense']['fumblesRecovered'] ?? 0),
            'solo_tackles' => $this->parseInt($stats['Defense']['soloTackles'] ?? 0),
            'defensive_interceptions' => $this->parseInt($stats['Defense']['defensiveInterceptions'] ?? 0),
            'qb_hits' => $this->parseInt($stats['Defense']['qbHits'] ?? 0),
            'tfl' => $this->parseInt($stats['Defense']['tfl'] ?? 0),
            'pass_deflections' => $this->parseInt($stats['Defense']['passDeflections'] ?? 0),
            'sacks' => $this->parseInt($stats['Defense']['sacks'] ?? 0),
            // Added Passing Stats
            'pass_yards' => $this->parseInt($stats['Passing']['passYds'] ?? 0),
            'pass_int' => $this->parseInt($stats['Passing']['int'] ?? 0),
            'pass_td' => $this->parseInt($stats['Passing']['passTD'] ?? 0),
            'pass_rtg' => $this->parseFloat($stats['Passing']['rtg'] ?? 0.0),
            'pass_qbr' => $this->parseFloat($stats['Passing']['qbr'] ?? 0.0),
            'pass_completions' => $this->parseInt($stats['Passing']['passCompletions'] ?? 0),
            'pass_attempts' => $this->parseInt($stats['Passing']['passAttempts'] ?? 0),
            'sacked' => $this->parseSacked($stats['Passing']['sacked'] ?? '0-0'),
            'pass_avg' => $this->parseFloat($stats['Passing']['passAvg'] ?? 0.0),
        ];
    }

    protected function parseInt($value): int
    {
        return is_numeric($value) ? (int)$value : 0;
    }

    protected function parseFloat($value)
    {
        return is_numeric($value) ? (float)$value : 0.0;
    }

    protected function parseSacked($value)
    {
        // Extract the number of times sacked from the string format "1-0"
        $parts = explode('-', $value);
        return isset($parts[0]) ? (int)$parts[0] : 0;
    }
}
