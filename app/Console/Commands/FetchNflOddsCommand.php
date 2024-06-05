<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OddsService;
use App\Services\OddsProcessingService;
use Symfony\Component\Console\Command\Command as CommandAlias;
use Illuminate\Support\Facades\Log;

class FetchNflOddsCommand extends Command
{
    protected $signature = 'odds:fetch-nfl';
    protected $description = 'Fetch NFL odds and store them in the database';
    protected $oddsService;
    protected $oddsProcessingService;

    public function __construct(OddsService $oddsService, OddsProcessingService $oddsProcessingService)
    {
        parent::__construct();
        $this->oddsService = $oddsService;
        $this->oddsProcessingService = $oddsProcessingService;
    }

    public function handle()
    {
        try {
            $odds = $this->oddsService->getOdds('americanfootball_nfl', 'h2h,spreads,totals');

            if (!empty($odds)) {
                $this->oddsProcessingService->processOdds($odds, \App\Models\NflTeam::class, \App\Models\NflOdds::class, \App\Models\NflOddsHistory::class);
                $this->info('NFL odds fetched and stored successfully.');
            } else {
                $this->warn('No NFL odds fetched.');
            }

            return CommandAlias::SUCCESS;
        } catch (\Exception $e) {
            Log::error('Error fetching NFL odds: ' . $e->getMessage());
            $this->error('Error fetching NFL odds. Check logs for details.');
            return CommandAlias::FAILURE;
        }
    }
}
