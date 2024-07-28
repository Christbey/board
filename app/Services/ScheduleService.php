<?php

namespace App\Services;

use App\Models\NflTeamSchedule;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class ScheduleService
{
    public function storeTeamSchedule(array $scheduleData): void
    {
        foreach ($scheduleData as $game) {
            if (!isset($game['gameID'])) {
                Log::error('Missing gameID in data: ', ['game' => $game]);
                continue;
            }

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

                Log::info('Team schedule data for game ' . $game['gameID'] . ' has been stored successfully.');
            } catch (Exception $e) {
                Log::error('Failed to store team schedule for game ' . $game['gameID'] . ': ' . $e->getMessage(), ['game' => $game]);
            }
        }
    }
}
