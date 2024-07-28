<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NFLStatsService;
use App\Services\Elo\EloRatingSystem;
use App\Services\ParsingService;
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
    protected ParsingService $parsingService;

    public function __construct(NFLStatsService $statsService, EloRatingSystem $eloRatingSystem, ParsingService $parsingService)
    {
        parent::__construct();
        $this->statsService = $statsService;
        $this->eloRatingSystem = $eloRatingSystem;
        $this->parsingService = $parsingService;
    }

    public function handle(): void
    {
        $gameID = $this->argument('gameID');
        $playByPlay = filter_var($this->option('playByPlay'), FILTER_VALIDATE_BOOLEAN);
        $fantasyPoints = filter_var($this->option('fantasyPoints'), FILTER_VALIDATE_BOOLEAN);

        $data = $this->statsService->getBoxScore($gameID, $playByPlay, $fantasyPoints);

        Log::info('Box Score Response:', ['data' => $data]);

        if ($data && $data['statusCode'] == 200) {
            $body = $data['body'];

            Log::info('Team Stats:', $body['teamStats'] ?? []);
            Log::info('Scoring Plays:', $body['scoringPlays'] ?? []);
            Log::info('All Play By Play:', $body['allPlayByPlay'] ?? []);

            $this->storePlayByPlayData($gameID, $body['allPlayByPlay'] ?? []);
            Log::info('Player Stats:', $body['playerStats'] ?? []);

            $this->info('Play-by-play data fetched and stored successfully.');
        } else {
            Log::error('Failed to fetch box score data:', ['response' => $data]);
            $this->error('Failed to fetch data.');
        }
    }

    protected function storePlayByPlayData(string $gameID, array $playByPlayData): void
    {
        foreach ($playByPlayData as $playData) {
            $playerStats = $playData['playerStats'] ?? [];

            foreach ($playerStats as $playerId => $stats) {
                $player = NflPlayer::where('player_id', $playerId)->first();

                if (!$player) {
                    Log::warning("Player ID $playerId not found in nfl_players table. Skipping.");
                    continue;
                }

                Log::info("Player ID: $playerId, Team Abbreviation: {$player->team}");

                $teamId = NflTeam::where('abbreviation', $player->team)->value('id');
                Log::info("Team Abbreviation: {$player->team}, Team ID: $teamId");

                if (!$teamId) {
                    Log::warning("Team abbreviation {$player->team} for player ID $playerId not found in nfl_teams table. Skipping.");
                    continue;
                }

                $expectedPointsBefore = null;
                $expectedPointsAfter = null;
                $epa = null;
                $startYardLine = null;
                $endYardLine = null;
                $playType = $this->parsingService->parsePlayType($playData['play'] ?? '');

                if (isset($playData['downAndDistance']) && isset($playData['play'])) {
                    $startFieldPosition = $this->parsingService->parseFieldPositionFromDownAndDistance($playData['downAndDistance']);
                    $endFieldPosition = $this->parsingService->parseEndFieldPositionFromPlay($playData['play'], $startFieldPosition, $playType);

                    if ($playType === 'Incomplete') {
                        $endFieldPosition = $startFieldPosition;
                    }

                    $expectedPointsBefore = $this->eloRatingSystem->getExpectedPoints($startFieldPosition);
                    $expectedPointsAfter = $this->eloRatingSystem->getExpectedPoints($endFieldPosition);
                    $epa = $expectedPointsAfter - $expectedPointsBefore;
                    $startYardLine = $startFieldPosition;
                    $endYardLine = $endFieldPosition;
                }

                $parsedDownAndDistance = $this->parsingService->parseDownDistance($playData['downAndDistance'] ?? '');
                $down = $parsedDownAndDistance['down'] ?? null;
                $distance = $parsedDownAndDistance['distance'] ?? null;
                $yardLine = $parsedDownAndDistance['yard_line'] ?? null;

                NflPlayByPlay::updateOrCreate(
                    [
                        'game_id' => (string)$gameID,
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
                        'team_id' => $teamId,
                        'expected_points_before' => $expectedPointsBefore,
                        'expected_points_after' => $expectedPointsAfter,
                        'epa' => $epa,
                        'down' => $down,
                        'distance' => $distance,
                        'yard_line' => $yardLine,
                        'play_type' => $playType,
                        'start_yard_line' => $startYardLine,
                        'end_yard_line' => $endYardLine
                    ]
                );
            }
        }
    }
}