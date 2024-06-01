<?php

namespace App\Jobs;

use App\Services\NcaaOddsService;
use App\Events\NcaaOddsFetched;

class FetchNcaaOdds extends FetchOddsJob
{
    public function __construct(NcaaOddsService $ncaaOddsService)
    {
        parent::__construct($ncaaOddsService, 'americanfootball_ncaaf', NcaaOddsFetched::class);
    }
}
