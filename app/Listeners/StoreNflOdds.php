<?php

namespace App\Listeners;

use App\Events\NflOddsFetched;
use App\Models\NflOdds;
use App\Models\NflOddsHistory;
use App\Models\NflTeam;

class StoreNflOdds extends BaseOddsListener
{
    public function handle(NflOddsFetched $event)
    {
        $this->handleOdds($event, NflOdds::class, NflOddsHistory::class, NflTeam::class);
    }
}
