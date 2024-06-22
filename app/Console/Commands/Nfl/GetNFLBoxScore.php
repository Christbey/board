<?php

namespace App\Console\Commands\Nfl;

use App\Models\NflTeamSchedule;
use Illuminate\Console\Command;
use App\Models\NflPlayerStat;
use App\Models\NflPlayer;
use Carbon\Carbon;
use App\Services\NFLStatsService;
use App\Traits\FormatsPlayerStats;

class GetNFLBoxScore extends Command
{
    use FormatsPlayerStats;

    protected $signature = 'nfl:fetch-boxscore {game_id?}';
    protected $description = 'Fetch NFL box score and store in database';
    protected NFLStatsService $statsService;

    public function __construct(NFLStatsService $statsService)
    {
        parent::__construct();
        $this->statsService = $statsService;
    }

    public function handle(): void
    {
        $gameID = $this->argument('game_id');

        if ($gameID) {
            // Process the specific game ID provided
            $this->info("Fetching box score for game: {$gameID}");
            $this->fetchAndStoreBoxScore($gameID);
        } else {
            // Retrieve all game IDs from the NflTeamSchedule table
            $gameIDs = NflTeamSchedule::pluck('game_id');

            foreach ($gameIDs as $gameID) {
                $this->fetchAndStoreBoxScore($gameID);
            }
        }
    }

    protected function fetchAndStoreBoxScore(string $gameID): void
    {
        $gameSchedule = NflTeamSchedule::where('game_id', $gameID)->first();

        if (!$gameSchedule) {
            $this->error("Game with ID {$gameID} not found.");
            return;
        }

        $gameDate = Carbon::parse($gameSchedule->game_date);
        $updatedAt = NflPlayerStat::where('game_id', $gameID)->max('updated_at');

        if ($gameDate->isFuture()) {
            $this->info("Skipping future game {$gameID}.");
            return;
        }

        // Check if the game date is today
        if ($gameDate->isToday()) {
            if ($updatedAt && Carbon::parse($updatedAt)->diffInMinutes(Carbon::now()) < 60) {
                $this->info("Data for game {$gameID} was fetched within the last hour. Skipping API call.");
                return;
            }
        } else {
            // Skip fetching if the game date is not today and stats already exist
            if (NflPlayerStat::where('game_id', $gameID)->exists()) {
                $this->info("Stats already exist for game {$gameID}. Skipping API call.");
                return;
            }
        }

        $data = $this->statsService->getBoxScore($gameID);

        if ($data && $data['statusCode'] == 200) {
            if (isset($data['body']['playerStats'])) {
                $this->savePlayerStats($data['body']['playerStats']);
                $this->info("NFL box score data for game {$gameID} fetched and stored successfully.");
            } else {
                $this->error("No player stats found for game {$gameID}.");
            }
        } else {
            $this->error("Failed to fetch NFL box score data for game {$gameID}.");
        }
    }

    protected function savePlayerStats(array $playerStats): void
    {
        foreach ($playerStats as $playerId => $player) {
            if (!NflPlayer::where('player_id', $playerId)->exists()) {
                // Create a record in the 'nfl_players' table
                NflPlayer::create(['player_id' => $playerId]);
                $this->info("Created new player record with ID {$playerId}.");
            }

            NflPlayerStat::updateOrCreate(
                ['player_id' => $playerId, 'game_id' => $player['gameID']],
                $this->formatPlayerStats($player)
            );
        }
    }
}