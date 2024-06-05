<?php

namespace App\Console\Commands;

use App\Models\NcaaOdds;
use App\Models\NcaaOddsHistory;
use App\Models\NcaaTeam;

class FetchNcaaOddsCommand extends FetchOddsCommand
{
    protected $signature = 'odds:fetch-ncaa';
    protected $description = 'NCAA odds';

    protected $sportKey = 'americanfootball_ncaaf';
    protected $teamModel = NcaaTeam::class;
    protected $oddsModel = NcaaOdds::class;
    protected $historyModel = NcaaOddsHistory::class;
}
