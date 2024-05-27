<?php

namespace App\Jobs;

use App\Services\NbaOddsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Events\NbaOddsFetched;

class FetchNbaOdds implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $nbaOddsService;

    public function __construct(NbaOddsService $nbaOddsService)
    {
        $this->nbaOddsService = $nbaOddsService;
    }

    public function handle()
    {
        $sport = 'basketball_nba';
        $markets = 'h2h,spreads,totals';

        // Fetch the odds
        $odds = $this->nbaOddsService->getOdds($sport, $markets);

        Log::info('Fetched NBA Odds: ' . json_encode($odds));

        // Dispatch event
        event(new NbaOddsFetched($odds));
    }
}