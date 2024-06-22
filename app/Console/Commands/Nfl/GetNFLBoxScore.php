<?php

namespace App\Console\Commands\Nfl;

use App\Models\NflTeamSchedule;
use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Models\NflPlayerStat;

class GetNFLBoxScore extends Command
{
    protected $signature = 'nfl:fetch-boxscore';
    protected $description = 'Fetch NFL box score and store in database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        // Retrieve all game IDs from the NflTeamSchedule table
        $gameIDs = NflTeamSchedule::pluck('game_id');

        foreach ($gameIDs as $gameID) {
            $client = new Client();
            $response = $client->request('GET', "https://tank01-nfl-live-in-game-real-time-statistics-nfl.p.rapidapi.com/getNFLBoxScore?gameID={$gameID}", [
                'headers' => [
                    'x-rapidapi-host' => 'tank01-nfl-live-in-game-real-time-statistics-nfl.p.rapidapi.com',
                    'x-rapidapi-key' => 'c737ce67c8msh02ec6008f7baf37p156f86jsn45e12a3b76f8',
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            if ($data['statusCode'] == 200) {
                $playerStats = $data['body']['playerStats'];
                foreach ($playerStats as $playerId => $player) {
                    NflPlayerStat::updateOrCreate(
                        ['player_id' => $playerId, 'game_id' => $player['gameID']],
                        [
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
                        ]
                    );
                }

                $this->info("NFL box score data for game {$gameID} fetched and stored successfully.");
            } else {
                $this->error("Failed to fetch NFL box score data for game {$gameID}.");
            }
        }
    }

    /**
     * Validate decimal value and return null if invalid
     *
     * @param mixed $value
     * @return float|null
     */
    private function validateDecimal($value)
    {
        return is_numeric($value) ? (float) $value : null;
    }
}