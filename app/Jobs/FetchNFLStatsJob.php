<?php

namespace App\Jobs;

use App\Models\NflPlayerStat;
use App\Models\NFLTeam;
use App\Services\NFLStatsService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class FetchNFLStatsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $playerId;
    protected $longName;

    public function __construct($playerId, $longName = null)
    {
        $this->playerId = $playerId;
        $this->longName = $longName;
    }

    public function handle(NFLStatsService $nflStatsService)
    {
        Log::info("Fetching stats for player ID: {$this->playerId}");

        $response = $nflStatsService->getNFLGamesForPlayer($this->playerId);

        if (is_null($response)) {
            Log::error("API Request failed or no data returned for player ID: {$this->playerId}");
            return;
        }

        $this->logRateLimitInfo($response);

        $games = $response['body'] ?? [];

        if (empty($games)) {
            Log::error("No game data found for player ID: {$this->playerId}");
            return;
        }

        foreach ($games as $gameID => $game) {
            if (isset($game['playerID'])) {
                $teamAbv = $game['teamAbv'] ?? '';
                $team = NFLTeam::where('abbreviation', $teamAbv)->first();

                if ($team) {
                    $stats = $this->extractStats($game);
                    NFLPlayerStat::updateOrCreate(
                        ['game_id' => $gameID, 'player_id' => $this->playerId],
                        array_merge(
                            [
                                'team_id' => $team->id,
                                'team_abv' => $teamAbv,
                                'player_name' => $game['longName'] ?? $this->longName
                            ],
                            $stats
                        )
                    );
                    Log::info('Player stats stored for player: ' . ($game['longName'] ?? $this->longName));
                } else {
                    Log::warning("Team not found for abbreviation: {$teamAbv}");
                }
            } else {
                Log::warning("Missing playerID for player ID: {$this->playerId}, Game ID: {$gameID}");
            }
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
            'pass_yards' => $game['Passing']['passYds'] ?? 0,
            'pass_int' => $game['Passing']['int'] ?? 0,
            'pass_td' => $game['Passing']['passTD'] ?? 0,
            'pass_rtg' => $game['Passing']['rtg'] ?? 0,
            'pass_qbr' => $game['Passing']['qbr'] ?? 0,
            'pass_completions' => $game['Passing']['passCompletions'] ?? 0,
            'pass_attempts' => $game['Passing']['passAttempts'] ?? 0,
            'sacked' => $game['Passing']['sacked'] ?? 0,
            'pass_avg' => $game['Passing']['passAvg'] ?? 0,
        ];
    }

    protected function logRateLimitInfo($response)
    {
        $headers = $response['headers'] ?? [];

        $rateLimitInfo = [
            'limit' => $headers['x-ratelimit-requests-limit'][0] ?? 'N/A',
            'remaining' => $headers['x-ratelimit-requests-remaining'][0] ?? 'N/A',
            'reset' => $headers['x-ratelimit-requests-reset'][0] ?? 'N/A',
        ];

        Log::info('Rate Limit Info: ', $rateLimitInfo);
    }
}
