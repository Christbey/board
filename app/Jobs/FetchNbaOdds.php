<?php

namespace App\Jobs;

use App\Services\NbaOddsService;
use App\Events\NbaOddsFetched;

class FetchNbaOdds extends FetchOddsJob
{
    public function __construct(NbaOddsService $nbaOddsService)
    {
        parent::__construct($nbaOddsService, 'basketball_nba', NbaOddsFetched::class);
    }
}
