<?php

namespace App\Console\Commands\Nfl;

use Illuminate\Console\Command;
use App\Services\NFLStatsService;
use App\Models\NflTeamSchedule;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class FetchNFLTeamSchedule extends Command
{
    protected $signature = 'fetch:nfl-team-schedule {teamAbv} {season}';
    protected $description = 'Fetch NFL team schedule for a given team and season';
    protected NFLStatsService $nflStatsService;

    public function __construct(NFLStatsService $nflStatsService)
    {
        parent::__construct();
        $this->nflStatsService = $nflStatsService;
    }

    public function handle(): void
    {
        $teamAbv = $this->argument('teamAbv');
        $season = $this->argument('season');

        $this->info('Fetching schedule for team: ' . $teamAbv . ' for season: ' . $season);

        $response = $this->nflStatsService->getNFLTeamSchedule($teamAbv, $season);
        $scheduleData = $response['body']['schedule'] ?? [];

        if (empty($scheduleData)) {
            $this->error('No schedule data found.');
            return;
        }

        // Log the response for debugging
        Log::info('API Response:', ['response' => $response]);

        // Store the schedule data
        $this->storeTeamSchedule($scheduleData);
    }

    protected function storeTeamSchedule(array $scheduleData)
    {
        foreach ($scheduleData as $game) {
            if (!isset($game['gameID'])) {
                Log::error('Missing gameID in data: ', ['game' => $game]);
                continue;
            }

            // Directly use the game time as retrieved from the API
            $gameTime = $game['gameTime'] ?? null;

            try {
                NflTeamSchedule::updateOrCreate(
                    ['game_id' => $game['gameID']],
                    [
                        'season_type' => $game['seasonType'] ?? 'N/A',
                        'away' => $game['away'] ?? 'N/A',
                        'team_id_home' => $game['teamIDHome'] ?? 'N/A',
                        'game_date' => Carbon::createFromFormat('Ymd', $game['gameDate'])->format('Y-m-d'),
                        'game_status' => $game['gameStatus'] ?? 'N/A',
                        'game_week' => $game['gameWeek'] ?? 'N/A',
                        'team_id_away' => $game['teamIDAway'] ?? 'N/A',
                        'home' => $game['home'] ?? 'N/A',
                        'away_result' => $game['awayResult'] ?? null,
                        'home_pts' => $game['homePts'] ?? 0,
                        'game_time' => $gameTime,
                        'home_result' => $game['homeResult'] ?? null,
                        'away_pts' => $game['awayPts'] ?? 0,
                    ]
                );

                $this->info('Team schedule data for game ' . $game['gameID'] . ' has been stored successfully.');
            } catch (\Exception $e) {
                Log::error('Failed to store team schedule for game ' . $game['gameID'] . ': ' . $e->getMessage(), ['game' => $game]);
            }
        }
    }
}
