<?php

namespace App\Jobs;

use App\Services\MlbOddsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Events\MlbOddsFetched;

class FetchMlbOdds implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $mlbOddsService;

    public function __construct(MlbOddsService $mlbOddsService)
    {
        $this->mlbOddsService = $mlbOddsService;
    }

    public function handle()
    {
        $sport = 'baseball_mlb';
        $markets = 'h2h,spreads,totals';

        // Fetch the odds
        $odds = $this->mlbOddsService->getOdds($sport, $markets);

        Log::info('Fetched MLB Odds: ' . json_encode($odds));

        // Dispatch event
        event(new MlbOddsFetched($odds));
    }
}