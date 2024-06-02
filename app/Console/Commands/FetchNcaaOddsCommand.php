<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NcaaOddsService;
use App\Jobs\FetchNcaaOdds;
use Symfony\Component\Console\Command\Command as CommandAlias;

class FetchNcaaOddsCommand extends Command
{
    protected $signature = 'odds:fetch-ncaa';
    protected $description = 'Fetch NCAA odds and store them in the database';

    public function handle()
    {
        $ncaaOddsService = app(NcaaOddsService::class);
        FetchNcaaOdds::dispatch($ncaaOddsService);

        $this->info('NCAA odds fetched and job dispatched successfully.');
        return CommandAlias::SUCCESS;
    }
}
