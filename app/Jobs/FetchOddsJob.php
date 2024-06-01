<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

abstract class FetchOddsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $oddsService;
    protected $sport;
    protected $markets;
    protected $eventClass;

    public function __construct($oddsService, $sport, $eventClass)
    {
        $this->oddsService = $oddsService;
        $this->sport = $sport;
        $this->markets = 'h2h,spreads,totals';
        $this->eventClass = $eventClass;
    }

    public function handle()
    {
        // Fetch the odds
        $odds = $this->oddsService->getOdds($this->sport, $this->markets);

        Log::info("Fetched {$this->sport} Odds: " . json_encode($odds));

        // Dispatch event
        event(new $this->eventClass($odds));
    }
}
