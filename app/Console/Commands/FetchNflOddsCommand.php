<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NflOddsService;
use App\Jobs\FetchNflOdds;
use Symfony\Component\Console\Command\Command as CommandAlias;

class FetchNflOddsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'odds:fetch-nfl';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch NFL odds and store them in the database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $nflOddsService = app(NflOddsService::class);
        FetchNflOdds::dispatch($nflOddsService);

        $this->info('NFL odds fetched and job dispatched successfully.');
        return CommandAlias::SUCCESS;
    }
}
