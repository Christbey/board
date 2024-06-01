<?php

namespace App\Jobs;

use App\Services\MlbOddsService;
use App\Events\MlbOddsFetched;

class FetchMlbOdds extends FetchOddsJob
{
    public function __construct(MlbOddsService $mlbOddsService)
    {
        parent::__construct($mlbOddsService, 'baseball_mlb', MlbOddsFetched::class);
    }
}
