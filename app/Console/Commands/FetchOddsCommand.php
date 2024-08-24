<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\FetchOddsJob;
use App\Services\OddsService;
use App\Services\OddsProcessingService;

class FetchOddsCommand extends Command
{
    protected $signature = 'odds:fetch {sport}';
    protected $description = 'Fetch the latest odds from the API for a given sport';

    public function handle(OddsService $oddsService, OddsProcessingService $oddsProcessingService): void
    {
        $sport = $this->argument('sport');
        FetchOddsJob::dispatch($sport, $oddsService, $oddsProcessingService);
        $this->info("FetchOddsJob for {$sport} dispatched.");
    }
}
