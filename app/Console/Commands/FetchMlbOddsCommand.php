<?php

namespace App\Console\Commands;

use App\Models\MlbOdds;
use App\Models\MlbOddsHistory;
use App\Models\MlbTeam;
use App\Services\OddsService;
use App\Services\OddsProcessingService;


class FetchMlbOddsCommand extends FetchOddsCommand
{
    protected $signature = 'odds:fetch-mlb';
    protected $description = 'MLB odds';

    protected $sportKey = 'baseball_mlb';
    protected $teamModel = MlbTeam::class;
    protected $oddsModel = MlbOdds::class;
    protected $historyModel = MlbOddsHistory::class;

    public function __construct(OddsService $oddsService, OddsProcessingService $oddsProcessingService)
    {
        parent::__construct($oddsService, $oddsProcessingService);
    }
}
