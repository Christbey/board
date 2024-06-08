<?php

// app/Traits/FetchOddsTrait.php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait FetchOddsTrait
{
    public function fetchAndStoreOdds($sportKey, $description, $teamModel, $oddsModel, $historyModel, $oddsService, $oddsProcessingService)
    {
        try {
            $odds = $oddsService->getOdds($sportKey, 'h2h,spreads,totals');

            if (!empty($odds)) {
                $oddsProcessingService->processOdds($odds, $teamModel, $oddsModel, $historyModel);
                $this->info("{$description} fetched and stored successfully.");
            } else {
                $this->warn("No {$description} fetched.");
            }

            return \Symfony\Component\Console\Command\Command::SUCCESS;
        } catch (\Exception $e) {
            Log::error("Error fetching {$description}: " . $e->getMessage());
            $this->error("Error fetching {$description}. Check logs for details.");
            return \Symfony\Component\Console\Command\Command::FAILURE;
        }
    }
}
