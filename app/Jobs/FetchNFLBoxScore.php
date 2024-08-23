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
use Illuminate\Bus\Batchable;

class FetchNFLBoxScore implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, FormatsPlayerStats, Batchable;

    protected string $gameID;

    public function __construct(string $gameID)
    {
        $this->gameID = $gameID;
    }

    public function handle(NFLStatsService $statsService)
    {
        if ($this->batch() && $this->batch()->canceled()) {
            return;
        }

        $gameSchedule = NflTeamSchedule::where('game_id', $this->gameID)->first();

        if (!$gameSchedule) {
            Log::error("Game with ID {$this->gameID} not found.");
            return;
        }

        $gameDate = Carbon::parse($gameSchedule->game_date);

        if ($gameSchedule->game_status === 'Completed' && $gameDate->isBefore(today())) {
            Log::info("Skipping completed game {$this->gameID} with a past date.");
            return;
        }

        if (!$gameDate->isToday()) {
            Log::info("Skipping game {$this->gameID} as it's not scheduled for today.");
            return;
        }

        if ($this->shouldSkipFetching($gameDate, $this->gameID)) {
            return;
        }

        $this->fetchAndStoreStats($statsService);
    }

    protected function shouldSkipFetching(Carbon $gameDate, string $gameID): bool
    {
        if ($gameDate->isToday()) {
            $updatedAt = NflPlayerStat::where('game_id', $gameID)->max('updated_at');
            if ($updatedAt && Carbon::parse($updatedAt)->diffInMinutes(now()) < 60) {
                Log::info("Data for game {$gameID} was fetched within the last hour. Skipping API call.");
                return true;
            }
        } elseif (NflPlayerStat::where('game_id', $gameID)->exists()) {
            Log::info("Stats already exist for game {$gameID}. Skipping API call.");
            return true;
        }

        return false;
    }

    protected function fetchAndStoreStats(NFLStatsService $statsService): void
    {
        $data = $statsService->getBoxScore($this->gameID);

        if (!$this->isValidResponse($data)) {
            Log::error("Failed to fetch NFL box score data for game {$this->gameID}.");
            return;
        }

        if (isset($data['body']['playerStats'])) {
            $this->savePlayerStats($data['body']['playerStats']);
            Log::info("NFL box score data for game {$this->gameID} fetched and stored successfully.");
        } else {
            Log::error("No player stats found for game {$this->gameID}.");
        }
    }

    protected function isValidResponse($data): bool
    {
        return $data && $data['statusCode'] == 200;
    }

    protected function savePlayerStats(array $playerStats): void
    {
        foreach ($playerStats as $playerId => $player) {
            NflPlayer::firstOrCreate(['player_id' => $playerId]);
            NflPlayerStat::updateOrCreate(
                ['player_id' => $playerId, 'game_id' => $player['gameID']],
                $this->formatPlayerStats($player)
            );
        }
    }
}
