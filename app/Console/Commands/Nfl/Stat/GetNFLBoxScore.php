<?php

namespace App\Console\Commands\Nfl\Stat;

use App\Jobs\Nfl\FetchNFLBoxScore;
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
            // Find game_ids not present in nfl_player_stats and game_date is within the last 2 weeks
            $gameIDs = NflTeamSchedule::where('game_date', '>=', now()->subWeeks(2))
                ->where('game_date', '<=', today())
                ->where('game_status', '!=', 'Completed')
                ->whereNotIn('game_id', function ($query) {
                    $query->select('game_id')->from('nfl_player_stats');
                })
                ->pluck('game_id');

            if ($gameIDs->isEmpty()) {
                $this->info('No games found without existing stats for the last two weeks.');
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
        $this->info('Dispatching batch job to fetch box scores for games.');

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
