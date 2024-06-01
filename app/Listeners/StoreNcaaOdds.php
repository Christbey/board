<?php

namespace App\Listeners;

use App\Events\NcaaOddsFetched;
use App\Models\NcaaOdds;
use App\Models\NcaaOddsHistory;
use App\Models\NcaaTeam;

class StoreNcaaOdds extends BaseOddsListener
{
    public function handle(NcaaOddsFetched $event)
    {
        $this->handleOdds($event, NcaaOdds::class, NcaaOddsHistory::class, NcaaTeam::class);
    }
}
