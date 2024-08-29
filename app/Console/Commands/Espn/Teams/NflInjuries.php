<?php

namespace App\Console\Commands\Espn\Teams;

use App\Jobs\Nfl\FetchNflInjuriesJob;
use App\Models\NflEspnTeam;
use Illuminate\Bus\Batch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Throwable;

class NflInjuries extends Command
{
    protected $signature = 'nfl:fetch-injuries';

    protected $description = 'Fetch NFL injuries for all teams and process them in a batch.';

    public function handle()
    {
        $teams = NflEspnTeam::all();

        $jobs = $teams->map(function ($team) {
            return new FetchNflInjuriesJob($team);
        })->toArray();

        Bus::batch($jobs)
            ->then(function (Batch $batch) {
                Log::info('All NFL injuries fetched successfully.');
            })
            ->catch(function (Batch $batch, Throwable $e) {
                Log::error('Fetching NFL injuries batch failed: ' . $e->getMessage());
            })
            ->finally(function (Batch $batch) {
                Log::info('Batch process finished.');
            })
            ->dispatch();

        $this->info('NFL injuries fetching jobs have been dispatched.');
    }
}
