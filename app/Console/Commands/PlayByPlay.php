<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NFLStatsService;
use App\Services\Elo\EloRatingSystem;
use App\Models\NflPlayByPlay;
use App\Models\NflTeam;
use App\Models\NflPlayer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class PlayByPlay extends Command
{
    protected $signature = 'playbyplay:fetch {gameID} {--playByPlay=true} {--fantasyPoints=false}';
    protected $description = 'Fetch and store NFL play-by-play data';

    protected NFLStatsService $statsService;
    protected EloRatingSystem $eloRatingSystem;

    public function __construct(NFLStatsService $statsService, EloRatingSystem $eloRatingSystem)
    {
        parent::__construct();
        $this->statsService = $statsService;
        $this->eloRatingSystem = $eloRatingSystem;
    }

    public function handle(): void
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
                // Check if player exists and get the team_id
                $player = NflPlayer::where('player_id', $playerId)->first();

                if (!$player) {
                    Log::warning("Player ID $playerId not found in nfl_players table. Skipping.");
                    continue; // Skip if player does not exist
                }

                // Log player team abbreviation
                Log::info("Player ID: $playerId, Team Abbreviation: {$player->team}");

                // Find the team_id using the team abbreviation
                $teamId = NflTeam::where('abbreviation', $player->team)->value('id');

                // Log the fetched team_id
                Log::info("Team Abbreviation: {$player->team}, Team ID: $teamId");

                if (!$teamId) {
                    Log::warning("Team abbreviation {$player->team} for player ID $playerId not found in nfl_teams table. Skipping.");
                    continue; // Skip if team does not exist
                }

                // Calculate EPA if 'downAndDistance' and 'play' keys exist
                $expectedPointsBefore = null;
                $expectedPointsAfter = null;
                $epa = null;
                $startYardLine = null;
                $endYardLine = null;
                $playType = $this->parsePlayType($playData['play'] ?? '');

                if (isset($playData['downAndDistance']) && isset($playData['play'])) {
                    $startFieldPosition = $this->eloRatingSystem->parseFieldPositionFromDownAndDistance($playData['downAndDistance']);
                    $endFieldPosition = $this->eloRatingSystem->parseFieldPositionFromPlay($playData['play']);

                    if ($playType === 'Incomplete') {
                        $endFieldPosition = $startFieldPosition; // For incomplete passes, end field position is the same as start field position
                    }

                    $expectedPointsBefore = $this->eloRatingSystem->getExpectedPoints($startFieldPosition);
                    $expectedPointsAfter = $this->eloRatingSystem->getExpectedPoints($endFieldPosition);
                    $epa = $expectedPointsAfter - $expectedPointsBefore;
                    $startYardLine = $startFieldPosition;
                    $endYardLine = $endFieldPosition;
                }

                // Parse additional fields
                $parsedDownAndDistance = $this->parseDownDistance($playData['downAndDistance'] ?? '');
                $down = $parsedDownAndDistance['down'] ?? null;
                $distance = $parsedDownAndDistance['distance'] ?? null;
                $yardLine = $parsedDownAndDistance['yard_line'] ?? null;

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
                        'team_id' => $teamId, // Store the team_id
                        'expected_points_before' => $expectedPointsBefore,
                        'expected_points_after' => $expectedPointsAfter,
                        'epa' => $epa,
                        'down' => $down, // Store the down
                        'distance' => $distance, // Store the distance
                        'yard_line' => $yardLine, // Store the yard line
                        'play_type' => $playType, // Store the play type
                        'start_yard_line' => $startYardLine, // Store the start yard line
                        'end_yard_line' => $endYardLine // Store the end yard line
                    ]
                );
            }
        }
    }

    protected function parseDownDistance(string $downAndDistance): array
    {
        $downDistancePattern = '/(\d+)(?:st|nd|rd|th)\s*&\s*(\d+|Goal)\s*at\s*([A-Z]{2,3})\s*(\d+)/i';
        preg_match($downDistancePattern, $downAndDistance, $matches);

        return [
            'down' => $matches[1] ?? null,
            'distance' => $matches[2] ?? null,
            'yard_line' => $matches[4] ?? null,
        ];
    }

    protected function parsePlayType(string $playDescription): string
    {
        $playTypePatterns = Config::get('nfl.playTypePatterns');

        if (str_contains(strtolower($playDescription), 'touchdown')) {
            return 'Touchdown';
        }

        foreach ($playTypePatterns as $pattern => $type) {
            if (str_contains(strtolower($playDescription), $pattern)) {
                return $type;
            }
        }

        return 'Unknown';
    }
}
