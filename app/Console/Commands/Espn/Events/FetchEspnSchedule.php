<?php

namespace App\Console\Commands\Espn\Events;

use App\Jobs\FetchEspnScheduleJob;

use Illuminate\Console\Command;


class FetchEspnSchedule extends Command
{
    protected $signature = 'fetch:espn-events {season_year} {season_type} {week_number}';
    protected $description = 'Fetch ESPN NFL events and store them in the database';

    public function handle()
    {
        $seasonYear = $this->argument('season_year');
        $seasonType = $this->argument('season_type');
        $weekNumber = $this->argument('week_number');

        // Dispatch the job with the provided arguments
        FetchEspnScheduleJob::dispatch($seasonYear, $seasonType, $weekNumber);

        $this->info('FetchEspnEvents job dispatched successfully.');
    }
}