<?php
// app/Console/Commands/FetchOddsCommand.php

namespace App\Console\Commands;

use App\Models\MlbOdds;
use App\Models\MlbOddsHistory;
use App\Models\MlbTeam;
use App\Models\NbaOdds;
use App\Models\NbaOddsHistory;
use App\Models\NbaTeam;
use App\Models\NcaaOdds;
use App\Models\NcaaOddsHistory;
use App\Models\NcaaTeam;
use App\Models\NflOdds;
use App\Models\NflOddsHistory;
use App\Models\NflTeam;
use Illuminate\Console\Command;
use App\Services\OddsService;
use App\Services\OddsProcessingService;
use App\Traits\FetchOddsTrait;

class FetchOddsCommand extends Command
{
    use FetchOddsTrait;

    protected $signature = 'odds:fetch {sport}';
    protected $description = 'Fetch the latest odds from the API for a given sport';

    protected $oddsService;
    protected $oddsProcessingService;

    public function __construct(OddsService $oddsService, OddsProcessingService $oddsProcessingService)
    {
        parent::__construct();
        $this->oddsService = $oddsService;
        $this->oddsProcessingService = $oddsProcessingService;
    }

    public function handle()
    {
        $sport = $this->argument('sport');

        $sportKey = $this->getSportKey($sport);
        $teamModel = $this->getTeamModelClass($sport);
        $oddsModel = $this->getOddsModelClass($sport);
        $historyModel = $this->getHistoryModelClass($sport);

        if ($sportKey && $teamModel && $oddsModel && $historyModel) {
            $this->fetchAndStoreOdds(
                $sportKey,
                $this->description,
                $teamModel,
                $oddsModel,
                $historyModel,
                $this->oddsService,
                $this->oddsProcessingService
            );
        } else {
            $this->error('Invalid sport provided.');
        }
    }

    protected function getSportKey($sport)
    {
        $sportKeys = [
            'mlb' => 'baseball_mlb',
            'nba' => 'basketball_nba',
            'nfl' => 'americanfootball_nfl',
            'ncaa' => 'americanfootball_ncaaf',
        ];

        return $sportKeys[$sport] ?? null;
    }

    protected function getTeamModelClass($sport)
    {
        $models = [
            'mlb' => MlbTeam::class,
            'nba' => NbaTeam::class,
            'nfl' => NflTeam::class,
            'ncaa' => NcaaTeam::class,
        ];

        return $models[$sport] ?? null;
    }

    protected function getOddsModelClass($sport)
    {
        $models = [
            'mlb' => MlbOdds::class,
            'nba' => NbaOdds::class,
            'nfl' => NflOdds::class,
            'ncaa' => NcaaOdds::class,
        ];

        return $models[$sport] ?? null;
    }

    protected function getHistoryModelClass($sport)
    {
        $models = [
            'mlb' => MlbOddsHistory::class,
            'nba' => NbaOddsHistory::class,
            'nfl' => NflOddsHistory::class,
            'ncaa' => NcaaOddsHistory::class,
        ];

        return $models[$sport] ?? null;
    }
}

