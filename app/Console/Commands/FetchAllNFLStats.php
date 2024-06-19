<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Jobs\FetchNFLStatsJob;

class FetchAllNFLStats extends Command
{
    protected $signature = 'fetch:all-nfl-stats {--limit=5}';
    protected $description = 'Fetch NFL stats for all players';

    public function handle(): void
    {
        $this->info('Fetching NFL stats for limited players...');

        $limit = $this->option('limit');
        $playerIds = DB::table('nfl_players')->limit($limit)->pluck('player_id');

        foreach ($playerIds as $playerId) {
            $this->info("Queuing stats fetch for player ID: $playerId");
            FetchNFLStatsJob::dispatch($playerId);
        }

        $this->info('Player stats fetch jobs have been queued successfully.');
    }
}
