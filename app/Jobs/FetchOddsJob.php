<?php

// app/Jobs/FetchOddsJob.php

namespace App\Jobs;

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
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\OddsService;
use App\Services\OddsProcessingService;
use App\Traits\FetchOddsTrait;
use Illuminate\Support\Facades\Log;

class FetchOddsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, FetchOddsTrait;

    protected $sport;
    protected OddsService $oddsService;
    protected OddsProcessingService $oddsProcessingService;

    public function __construct($sport, OddsService $oddsService, OddsProcessingService $oddsProcessingService)
    {
        $this->sport = $sport;
        $this->oddsService = $oddsService;
        $this->oddsProcessingService = $oddsProcessingService;
    }

    public function handle(): void
    {
        $sport = $this->sport;

        Log::info("Starting FetchOddsJob for $sport");

        $sportKey = $this->getSportKey($sport);
        $teamModel = $this->getTeamModelClass($sport);
        $oddsModel = $this->getOddsModelClass($sport);
        $historyModel = $this->getHistoryModelClass($sport);

        if ($sportKey && $teamModel && $oddsModel && $historyModel) {
            $this->fetchAndStoreOdds(
                $sportKey,
                "Fetching odds for $sport",
                $teamModel,
                $oddsModel,
                $historyModel,
                $this->oddsService,
                $this->oddsProcessingService
            );
            Log::info("Completed FetchOddsJob for $sport");
        } else {
            Log::error('Invalid sport provided.');
        }
    }

    protected function getSportKey($sport): ?string
    {
        $sportKeys = [
            'mlb' => 'baseball_mlb',
            'nba' => 'basketball_nba',
            'nfl' => 'americanfootball_nfl',
            'ncaa' => 'americanfootball_ncaaf',
        ];

        return $sportKeys[$sport] ?? null;
    }

    protected function getTeamModelClass($sport): ?string
    {
        $models = [
            'mlb' => MlbTeam::class,
            'nba' => NbaTeam::class,
            'nfl' => NflTeam::class,
            'ncaa' => NcaaTeam::class,
        ];

        return $models[$sport] ?? null;
    }

    protected function getOddsModelClass($sport): ?string
    {
        $models = [
            'mlb' => MlbOdds::class,
            'nba' => NbaOdds::class,
            'nfl' => NflOdds::class,
            'ncaa' => NcaaOdds::class,
        ];

        return $models[$sport] ?? null;
    }

    protected function getHistoryModelClass($sport): ?string
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
