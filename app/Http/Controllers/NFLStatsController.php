<?php

namespace App\Http\Controllers;

use App\Models\NflPlayByPlay;
use App\Models\NflPlayer;
use App\Services\NFLStatsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NFLStatsController extends Controller
{
    protected NFLStatsService $statsService;

    public function __construct(NFLStatsService $statsService)
    {
        $this->statsService = $statsService;
    }

    public function fetchBoxScore(Request $request, $gameID)
    {
        $playByPlay = filter_var($request->query('playByPlay', 'true'), FILTER_VALIDATE_BOOLEAN);
        $fantasyPoints = filter_var($request->query('fantasyPoints', 'false'), FILTER_VALIDATE_BOOLEAN);

        $data = $this->statsService->getBoxScore($gameID, $playByPlay, $fantasyPoints);

        // Log the raw response for debugging
        Log::info('Box Score Response:', ['data' => $data]);

        if ($data && $data['statusCode'] == 200) {
            $body = $data['body'];

            // Extract and process team stats
            $teamStats = $body['teamStats'] ?? [];
            Log::info('Team Stats:', $teamStats);

            // Extract and process scoring plays
            $scoringPlays = $body['scoringPlays'] ?? [];
            Log::info('Scoring Plays:', $scoringPlays);

            // Extract and process play-by-play data
            $allPlayByPlay = $body['allPlayByPlay'] ?? [];
            Log::info('All Play By Play:', $allPlayByPlay);

            $this->storePlayByPlayData($gameID, $allPlayByPlay);

            // Extract player stats if available
            $playerStats = $body['playerStats'] ?? [];
            Log::info('Player Stats:', $playerStats);

            // Return the processed data as a JSON response
            return response()->json([
                'gameStatus' => $body['gameStatus'] ?? 'Unknown',
                'gameDate' => $body['gameDate'] ?? 'Unknown',
                'teamStats' => $teamStats,
                'scoringPlays' => $scoringPlays,
                'allPlayByPlay' => $allPlayByPlay,
                'playerStats' => $playerStats,
            ]);
        } else {
            // Log the error response
            Log::error('Failed to fetch box score data:', ['response' => $data]);
            return response()->json(['error' => 'Failed to fetch data.'], 500);
        }
    }

    protected function storePlayByPlayData(string $gameID, array $playByPlayData)
    {
        foreach ($playByPlayData as $playData) {
            $playerStats = $playData['playerStats'] ?? [];

            foreach ($playerStats as $playerId => $stats) {
                // Check if player exists
                if (!NflPlayer::where('player_id', $playerId)->exists()) {
                    Log::warning("Player ID $playerId not found in nfl_players table. Skipping.");
                    continue; // Skip if player does not exist
                }

                NflPlaybyPlay::updateOrCreate([
                        'game_id' => (string)$gameID, // Cast to string
                        'player_id' => $playerId,
                        'play_period' => $playData['playPeriod'] ?? null,
                        'play_clock' => $playData['playClock'] ?? null,
                        'play' => $playData['play'] ?? null,
                        'kick_yards' => $stats['Kicking']['kickYards'] ?? null,
                        'fg_attempts' => $stats['Kicking']['fgAttempts'] ?? null,
                        'fg_yds' => $stats['Kicking']['fgYds'] ?? null,
                        'receptions' => $stats['Receiving']['receptions'] ?? null,
                        'targets' => $stats['Receiving']['targets'] ?? null,
                        'rec_yds' => $stats['Receiving']['recYds'] ?? null,
                        'pass_attempts' => $stats['Passing']['passAttempts'] ?? null,
                        'pass_yds' => $stats['Passing']['passYds'] ?? null,
                        'pass_completions' => $stats['Passing']['passCompletions'] ?? null,
                        'rush_yds' => $stats['Rushing']['rushYds'] ?? null,
                        'carries' => $stats['Rushing']['carries'] ?? null,
                        'down_and_distance' => $playData['downAndDistance'] ?? null,
                    ]
                );
            }
        }
    }
}