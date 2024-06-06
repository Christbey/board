<?php

namespace App\Console\Commands;

use App\Services\OddsService;
use App\Services\OddsProcessingService;
use Symfony\Component\Console\Command\Command as CommandAlias;
use Illuminate\Support\Facades\Log;

class FetchMlbOddsCommand extends FetchOddsCommand
{
    protected $signature = 'odds:fetch-mlb';
    protected $description = 'MLB odds';

    protected $sportKey = 'baseball_mlb';
    protected $teamModel = \App\Models\MlbTeam::class;
    protected $oddsModel = \App\Models\MlbOdds::class;
    protected $historyModel = \App\Models\MlbOddsHistory::class;

    public function __construct(OddsService $oddsService, OddsProcessingService $oddsProcessingService)
    {
        parent::__construct($oddsService, $oddsProcessingService);
    }
}
