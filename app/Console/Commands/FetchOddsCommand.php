<?php
// app/Console/Commands/FetchOddsCommand.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\FetchOddsJob;
use App\Services\OddsService;
use App\Services\OddsProcessingService;

class FetchOddsCommand extends Command
{
    protected $signature = 'odds:fetch {sport}';
    protected $description = 'Fetch the latest odds from the API for a given sport';

    protected OddsService $oddsService;
    protected OddsProcessingService $oddsProcessingService;

    public function __construct(OddsService $oddsService, OddsProcessingService $oddsProcessingService)
    {
        parent::__construct();
        $this->oddsService = $oddsService;
        $this->oddsProcessingService = $oddsProcessingService;
    }

    public function handle(): void
    {
        $sport = $this->argument('sport');
        FetchOddsJob::dispatch($sport, $this->oddsService, $this->oddsProcessingService);
        $this->info("FetchOddsJob for {$sport} dispatched.");
    }
}

