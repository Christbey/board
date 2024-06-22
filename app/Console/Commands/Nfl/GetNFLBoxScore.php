<?php

namespace App\Console\Commands\Nfl;

use App\Models\NflTeamSchedule;
use Illuminate\Console\Command;
use App\Models\NflPlayerStat;
use App\Models\NflPlayer;
use Carbon\Carbon;
use App\Services\NFLStatsService;

class GetNFLBoxScore extends Command
{
    protected $signature = 'nfl:fetch-boxscore';
    protected $description = 'Fetch NFL box score and store in database';
    protected NFLStatsService $statsService;

    public function __construct(NFLStatsService $statsService)
    {
        parent::__construct();
        $this->statsService = $statsService;
    }

    public function handle(): void
    {
        $gameIDs = NflTeamSchedule::pluck('game_id');

        foreach ($gameIDs as $gameID) {
            $this->fetchAndStoreBoxScore($gameID);
        }
    }

    protected function fetchAndStoreBoxScore(string $gameID): void
    {
        $data = $this->statsService->getBoxScore($gameID);

        if ($data && $data['statusCode'] == 200) {
            if (isset($data['body']['playerStats'])) {
                $gameDate = NflTeamSchedule::where('game_id', $gameID)->value('game_date');

                if ($gameDate && Carbon::parse($gameDate)->isFuture()) {
                    $this->info("Skipping future game {$gameID}.");
                    return;
                }

                $this->savePlayerStats($data['body']['playerStats']);
                $this->info("NFL box score data for game {$gameID} fetched and stored successfully.");
            } else {
                $this->error("No player stats found for game {$gameID}.");
            }
        } else {
            $this->error("Failed to fetch NFL box score data for game {$gameID}.");
        }
    }

    protected function savePlayerStats(array $playerStats): void
    {
        foreach ($playerStats as $playerId => $player) {
            if (!NflPlayer::where('player_id', $playerId)->exists()) {
                // Create a new record in the nfl_players table
                NflPlayer::create(['player_id' => $playerId]);
                $this->info("Created new player record with ID {$playerId}.");
            }

            NflPlayerStat::updateOrCreate(
                ['player_id' => $playerId, 'game_id' => $player['gameID']],
                $this->formatPlayerStats($player)
            );
        }
    }

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
