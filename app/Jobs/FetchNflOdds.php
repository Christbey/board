<?php

namespace App\Jobs;

use App\Services\NflOddsService;
use App\Events\NflOddsFetched;

class FetchNflOdds extends FetchOddsJob
{
    public function __construct(NflOddsService $nflOddsService)
    {
        parent::__construct($nflOddsService, 'americanfootball_nfl', NflOddsFetched::class);
    }
}
