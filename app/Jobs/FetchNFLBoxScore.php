<?php

namespace App\Jobs;

use App\Models\NflTeamSchedule;
use App\Models\NflPlayerStat;
use App\Models\NflPlayer;
use Carbon\Carbon;
use App\Services\NFLStatsService;
use App\Traits\FormatsPlayerStats;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FetchNFLBoxScore implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, FormatsPlayerStats;

    protected string $gameID;

    public function __construct(string $gameID)
    {
        $this->gameID = $gameID;
    }

    public function handle(NFLStatsService $statsService)
    {
        $gameSchedule = NflTeamSchedule::where('game_id', $this->gameID)->first();

        if (!$gameSchedule) {
            Log::error("Game with ID {$this->gameID} not found.");
            return;
        }

        $gameDate = Carbon::parse($gameSchedule->game_date);
        $updatedAt = NflPlayerStat::where('game_id', $this->gameID)->max('updated_at');

        if ($gameDate->isFuture()) {
            Log::info("Skipping future game {$this->gameID}.");
            return;
        }

        if ($this->shouldSkipFetching($gameDate, $updatedAt)) {
            return;
        }

        $data = $statsService->getBoxScore($this->gameID);

        if ($this->isValidResponse($data)) {
            if (isset($data['body']['playerStats'])) {
                $this->savePlayerStats($data['body']['playerStats']);
                Log::info("NFL box score data for game {$this->gameID} fetched and stored successfully.");
            } else {
                Log::error("No player stats found for game {$this->gameID}.");
            }
        } else {
            Log::error("Failed to fetch NFL box score data for game {$this->gameID}.");
        }
    }

    protected function shouldSkipFetching(Carbon $gameDate, ?string $updatedAt): bool
    {
        if ($gameDate->isToday()) {
            if ($updatedAt && Carbon::parse($updatedAt)->diffInMinutes(Carbon::now()) < 60) {
                Log::info("Data for game {$this->gameID} was fetched within the last hour. Skipping API call.");
                return true;
            }
        } elseif (NflPlayerStat::where('game_id', $this->gameID)->exists()) {
            Log::info("Stats already exist for game {$this->gameID}. Skipping API call.");
            return true;
        }

        return false;
    }

    protected function isValidResponse($data): bool
    {
        return $data && $data['statusCode'] == 200;
    }

    protected function savePlayerStats(array $playerStats)
    {
        foreach ($playerStats as $playerId => $player) {
            if (!NflPlayer::where('player_id', $playerId)->exists()) {
                NflPlayer::create(['player_id' => $playerId]);
                Log::info("Created new player record with ID {$playerId}.");
            }

            NflPlayerStat::updateOrCreate(
                ['player_id' => $playerId, 'game_id' => $player['gameID']],
                $this->formatPlayerStats($player)
            );
        }
    }
}
