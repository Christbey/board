<?php

namespace App\Listeners;

use App\Events\NbaOddsFetched;
use App\Models\NbaOdds;
use App\Models\NbaOddsHistory;
use App\Models\NbaTeam;

class StoreNbaOdds extends BaseOddsListener
{
    public function handle(NbaOddsFetched $event)
    {
        $this->handleOdds($event, NbaOdds::class, NbaOddsHistory::class, NbaTeam::class);
    }
}
