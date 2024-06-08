<?php

// app/Jobs/FetchOddsJob.php

namespace App\Jobs;

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
    protected $oddsService;
    protected $oddsProcessingService;

    public function __construct($sport, OddsService $oddsService, OddsProcessingService $oddsProcessingService)
    {
        $this->sport = $sport;
        $this->oddsService = $oddsService;
        $this->oddsProcessingService = $oddsProcessingService;
    }

    public function handle()
    {
        $sport = $this->sport;

        Log::info("Starting FetchOddsJob for {$sport}");

        $sportKey = $this->getSportKey($sport);
        $teamModel = $this->getTeamModelClass($sport);
        $oddsModel = $this->getOddsModelClass($sport);
        $historyModel = $this->getHistoryModelClass($sport);

        if ($sportKey && $teamModel && $oddsModel && $historyModel) {
            $this->fetchAndStoreOdds(
                $sportKey,
                "Fetching odds for {$sport}",
                $teamModel,
                $oddsModel,
                $historyModel,
                $this->oddsService,
                $this->oddsProcessingService
            );
            Log::info("Completed FetchOddsJob for {$sport}");
        } else {
            Log::error('Invalid sport provided.');
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
            'mlb' => \App\Models\MlbTeam::class,
            'nba' => \App\Models\NbaTeam::class,
            'nfl' => \App\Models\NflTeam::class,
            'ncaa' => \App\Models\NcaaTeam::class,
        ];

        return $models[$sport] ?? null;
    }

    protected function getOddsModelClass($sport)
    {
        $models = [
            'mlb' => \App\Models\MlbOdds::class,
            'nba' => \App\Models\NbaOdds::class,
            'nfl' => \App\Models\NflOdds::class,
            'ncaa' => \App\Models\NcaaOdds::class,
        ];

        return $models[$sport] ?? null;
    }

    protected function getHistoryModelClass($sport)
    {
        $models = [
            'mlb' => \App\Models\MlbOddsHistory::class,
            'nba' => \App\Models\NbaOddsHistory::class,
            'nfl' => \App\Models\NflOddsHistory::class,
            'ncaa' => \App\Models\NcaaOddsHistory::class,
        ];

        return $models[$sport] ?? null;
    }
}
