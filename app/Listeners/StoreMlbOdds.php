<?php

namespace App\Listeners;

use App\Events\MlbOddsFetched;
use App\Models\MlbOdds;
use App\Models\MlbOddsHistory;
use App\Models\MlbTeam;
use Illuminate\Support\Facades\Log;
use App\Listeners\BaseOddsListener;
use Carbon\Carbon;

class StoreMlbOdds extends BaseOddsListener
{
    public function handle(MlbOddsFetched $event)
    {
        $this->handleOdds($event, MlbOdds::class, MlbOddsHistory::class, MlbTeam::class);
    }
}
