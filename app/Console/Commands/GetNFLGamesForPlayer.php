<?php

namespace App\Console\Commands;

use App\Models\NflPlayerStat;
use App\Models\NFLTeam;
use Illuminate\Console\Command;
use App\Services\NFLStatsService;

class GetNFLGamesForPlayer extends Command
{
    protected $signature = 'nfl:get-games-for-player {playerID}';
    protected $description = 'Get NFL Games for a player';
    protected NFLStatsService $nflStatsService;

    public function __construct(NFLStatsService $nflStatsService)
    {
        parent::__construct();
        $this->nflStatsService = $nflStatsService;
    }

    public function handle()
    {
        $playerID = $this->argument('playerID');
        $this->info('Fetching NFL games for player ID: ' . $playerID);

        $response = $this->nflStatsService->getNFLGamesForPlayer($playerID);
        $games = $response['body'] ?? [];

        if (empty($games)) {
            $this->error('No game data found.');
            return;
        }

        foreach ($games as $gameID => $game) {
            if (!isset($game['playerID'], $game['longName'])) {
                continue; // Skip this iteration if player name is not found
            }

            $teamAbv = $game['teamAbv'];
            $team = NFLTeam::where('abbreviation', $teamAbv)->first();

            if (!$team) {
                $this->error('Team not found for abbreviation: ' . $teamAbv);
                continue;
            }

            $stats = $this->extractStats($game);

            NFLPlayerStat::updateOrCreate(
                ['game_id' => $gameID, 'player_id' => $playerID],
                array_merge(
                    ['team_id' => $team->id, 'team_abv' => $teamAbv, 'player_name' => $game['longName']],
                    $stats
                )
            );

            $this->info('Player stats stored for player: ' . $game['longName']);
        }
    }

    protected function extractStats(array $game): array
    {
        return [
            'rush_yards' => $game['Rushing']['rushYds'] ?? 0,
            'carries' => $game['Rushing']['carries'] ?? 0,
            'rush_td' => $game['Rushing']['rushTD'] ?? 0,
            'receptions' => $game['Receiving']['receptions'] ?? 0,
            'rec_td' => $game['Receiving']['recTD'] ?? 0,
            'targets' => $game['Receiving']['targets'] ?? 0,
            'rec_yards' => $game['Receiving']['recYds'] ?? 0,
            'games_played' => $game['Receiving']['gamesPlayed'] ?? 0,
            'total_tackles' => $game['Defense']['totalTackles'] ?? 0,
            'fumbles_lost' => $game['Defense']['fumblesLost'] ?? 0,
            'def_td' => $game['Defense']['defTD'] ?? 0,
            'fumbles' => $game['Defense']['fumbles'] ?? 0,
            'fumbles_recovered' => $game['Defense']['fumblesRecovered'] ?? 0,
            'solo_tackles' => $game['Defense']['soloTackles'] ?? 0,
            'defensive_interceptions' => $game['Defense']['defensiveInterceptions'] ?? 0,
            'qb_hits' => $game['Defense']['qbHits'] ?? 0,
            'tfl' => $game['Defense']['tfl'] ?? 0,
            'pass_deflections' => $game['Defense']['passDeflections'] ?? 0,
            'sacks' => $game['Defense']['sacks'] ?? 0,
        ];
    }
}
