<?php

namespace App\Jobs;

use App\Services\NflOddsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Events\NflOddsFetched;

class FetchNflOdds implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $nflOddsService;

    public function __construct(NflOddsService $nflOddsService)
    {
        $this->nflOddsService = $nflOddsService;
    }

    public function handle()
    {
        $sport = 'americanfootball_nfl';
        $markets = 'h2h,spreads,totals';

        // Fetch the odds
        $odds = $this->nflOddsService->getOdds($sport, $markets);

        Log::info('Fetched NFL Odds: ' . json_encode($odds));

        // Dispatch event
        event(new NflOddsFetched($odds));
    }
}
