<?php

namespace App\Jobs;

use App\Services\NcaaOddsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Events\OddsFetched;

class FetchNcaaOdds implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $ncaaOddsService;

    public function __construct(NcaaOddsService $ncaaOddsService)
    {
        $this->ncaaOddsService = $ncaaOddsService;
    }

    public function handle()
    {
        $sport = 'americanfootball_ncaaf';
        $markets = 'h2h,spreads,totals';

        // Fetch the odds
        $odds = $this->ncaaOddsService->getOdds($sport, $markets);

        // Log the fetched odds
        Log::info('Fetched NCAAF Odds: ' . json_encode($odds));

        // Dispatch an event to store the odds
        event(new OddsFetched($odds));
    }
}
