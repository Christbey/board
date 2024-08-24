<?php

namespace App\Jobs;

use App\Services\OddsService;
use App\Services\OddsProcessingService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
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

        $config = config("sports.$sport");

        if ($config) {
            $this->fetchAndStoreOdds(
                $config['sport_key'],
                "Fetching odds for $sport",
                $config['team_model'],
                $config['odds_model'],
                $config['history_model'],
                $this->oddsService,
                $this->oddsProcessingService
            );
            Log::info("Completed FetchOddsJob for $sport");
        } else {
            Log::error('Invalid sport provided.');
        }
    }
}
