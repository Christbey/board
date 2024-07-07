<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NFLStatsService;
use App\Models\NflPlayByPlay;
use App\Models\NflPlayer;
use Illuminate\Support\Facades\Log;

class PlayByPlay extends Command
{
    protected $signature = 'playbyplay:fetch {gameID} {--playByPlay=true} {--fantasyPoints=false}';
    protected $description = 'Fetch and store NFL play-by-play data';

    protected NFLStatsService $statsService;

    public function __construct(NFLStatsService $statsService)
    {
        parent::__construct();
        $this->statsService = $statsService;
    }

    public function handle()
    {
        $gameID = $this->argument('gameID');
        $playByPlay = filter_var($this->option('playByPlay'), FILTER_VALIDATE_BOOLEAN);
        $fantasyPoints = filter_var($this->option('fantasyPoints'), FILTER_VALIDATE_BOOLEAN);

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

            $this->info('Play-by-play data fetched and stored successfully.');
        } else {
            // Log the error response
            Log::error('Failed to fetch box score data:', ['response' => $data]);
            $this->error('Failed to fetch data.');
        }
    }

    protected function storePlayByPlayData(string $gameID, array $playByPlayData): void
    {
        foreach ($playByPlayData as $playData) {
            $playerStats = $playData['playerStats'] ?? [];

            foreach ($playerStats as $playerId => $stats) {
                // Check if player exists
                if (!NflPlayer::where('player_id', $playerId)->exists()) {
                    Log::warning("Player ID $playerId not found in nfl_players table. Skipping.");
                    continue; // Skip if player does not exist
                }

                NflPlayByPlay::updateOrCreate(
                    [
                        'game_id' => (string)$gameID, // Cast to string
                        'player_id' => $playerId,
                        'play_period' => $playData['playPeriod'] ?? null,
                        'play_clock' => $playData['playClock'] ?? null,
                    ],
                    [
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
