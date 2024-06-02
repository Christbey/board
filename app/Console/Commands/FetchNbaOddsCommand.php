<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NbaOddsService;
use App\Jobs\FetchNbaOdds;
use Symfony\Component\Console\Command\Command as CommandAlias;

class FetchNbaOddsCommand extends Command
{
    protected $signature = 'odds:fetch-nba';
    protected $description = 'Fetch NBA odds and store them in the database';

    public function handle()
    {
        $nbaOddsService = app(NbaOddsService::class);
        FetchNbaOdds::dispatch($nbaOddsService);

        $this->info('NBA odds fetched and job dispatched successfully.');
        return CommandAlias::SUCCESS;
    }
}
