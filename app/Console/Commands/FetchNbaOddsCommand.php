<?php

namespace App\Console\Commands;

use App\Models\NbaOdds;
use App\Models\NbaOddsHistory;
use App\Models\NbaTeam;

class FetchNbaOddsCommand extends FetchOddsCommand
{
    protected $signature = 'odds:fetch-nba';
    protected $description = 'NBA odds';

    protected $sportKey = 'basketball_nba';
    protected $teamModel = NbaTeam::class;
    protected $oddsModel = NbaOdds::class;
    protected $historyModel = NbaOddsHistory::class;
}
