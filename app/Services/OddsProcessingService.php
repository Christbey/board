<?php

namespace App\Services;

use App\Traits\ProcessesOdds;
use App\Traits\CompareOdds;

class OddsProcessingService
{
    use ProcessesOdds, CompareOdds;

    public function processNflOdds(array $odds)
    {
        $this->processOdds($odds, \App\Models\NflTeam::class, \App\Models\NflOdds::class, \App\Models\NflOddsHistory::class);
    }

    // Add similar methods for other sports as needed
}
