<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OddsService;
use App\Services\OddsProcessingService;
use Symfony\Component\Console\Command\Command as CommandAlias;
use Illuminate\Support\Facades\Log;

abstract class FetchOddsCommand extends Command
{
    protected $oddsService;
    protected $oddsProcessingService;
    protected $sportKey;
    protected $teamModel;
    protected $oddsModel;
    protected $historyModel;

    public function __construct(OddsService $oddsService, OddsProcessingService $oddsProcessingService)
    {
        parent::__construct();
        $this->oddsService = $oddsService;
        $this->oddsProcessingService = $oddsProcessingService;
    }

    public function handle()
    {
        try {
            $odds = $this->oddsService->getOdds($this->sportKey, 'h2h,spreads,totals');

            if (!empty($odds)) {
                $this->oddsProcessingService->processOdds($odds, $this->teamModel, $this->oddsModel, $this->historyModel);
                $this->info("{$this->description} fetched and stored successfully.");
            } else {
                $this->warn("No {$this->description} fetched.");
            }

            return CommandAlias::SUCCESS;
        } catch (\Exception $e) {
            Log::error("Error fetching {$this->description}: " . $e->getMessage());
            $this->error("Error fetching {$this->description}. Check logs for details.");
            return CommandAlias::FAILURE;
        }
    }
}
