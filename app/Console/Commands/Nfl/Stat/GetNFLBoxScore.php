<?php

namespace App\Console\Commands\Nfl\Stat;

use App\Jobs\FetchNFLBoxScore;
use App\Models\NflTeamSchedule;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

class GetNFLBoxScore extends Command
{
    protected $signature = 'nfl:fetch-boxscore {game_id?}';
    protected $description = 'Fetch NFL box score and store in database';

    public function handle(): void
    {
        $gameID = $this->argument('game_id');

        if ($gameID) {
            $this->dispatchJob($gameID);
        } else {
            $gameIDs = NflTeamSchedule::where('game_date', today())
                ->where('game_status', '!=', 'Completed')
                ->pluck('game_id');

            if ($gameIDs->isEmpty()) {
                $this->info('No games scheduled for today or all games have been completed.');
                return;
            }

            $this->dispatchBatch($gameIDs->all());
        }
    }

    private function dispatchJob(string $gameID): void
    {
        $this->info("Dispatching job to fetch box score for game: {$gameID}");
        FetchNFLBoxScore::dispatch($gameID);
    }

    private function dispatchBatch(array $gameIDs): void
    {
        $this->info("Dispatching batch job to fetch box scores for today's games.");

        Bus::batch(
            array_map(fn($gameID) => new FetchNFLBoxScore($gameID), $gameIDs)
        )->then(function () {
            Log::info('All NFL box score jobs have been processed.');
        })->catch(function () {
            Log::error('One or more NFL box score jobs have failed.');
        })->finally(function () {
            Log::info('The NFL box score job batch has completed.');
        })->dispatch();
    }
}
