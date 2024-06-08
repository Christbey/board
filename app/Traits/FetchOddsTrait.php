<?php

// app/Traits/FetchOddsTrait.php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait FetchOddsTrait
{
    public function fetchAndStoreOdds(
        $sportKey,
        $description,
        $teamModel,
        $oddsModel,
        $historyModel,
        $oddsService,
        $oddsProcessingService
    ) {
        Log::info("Starting to fetch odds for {$sportKey}");

        try {
            $odds = $oddsService->getOdds($sportKey, 'h2h,spreads,totals');
            if (!empty($odds)) {
                $oddsProcessingService->processOdds($odds, $teamModel, $oddsModel, $historyModel);
                Log::info("{$description} fetched and stored successfully.");
            } else {
                Log::warning("No {$description} fetched.");
            }
        } catch (\Exception $e) {
            Log::error("Error fetching {$description}: " . $e->getMessage());
        }
    }
}
