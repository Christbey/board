<?php

namespace App\Console\Commands;

use App\Models\NflOdds;
use App\Models\NflOddsHistory;
use App\Models\NflTeam;

class FetchNflOddsCommand extends FetchOddsCommand
{
    protected $signature = 'odds:fetch-nfl';
    protected $description = 'NFL odds';

    protected $sportKey = 'americanfootball_nfl';
    protected $teamModel = NflTeam::class;
    protected $oddsModel = NflOdds::class;
    protected $historyModel = NflOddsHistory::class;
}
