<?php

namespace App\Jobs;

use App\Services\NflOddsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Events\OddsFetched;

class FetchNflOdds implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(NflOddsService $nflOddsService)
    {
        $sport = 'americanfootball_nfl';
        $markets = 'h2h,spreads,totals';

        // Fetch the odds
        $odds = $nflOddsService->getOdds($sport, $markets);

        Log::info('Fetched NFL Odds: ' . json_encode($odds));

        // Dispatch event
        event(new OddsFetched($odds));
    }
}
