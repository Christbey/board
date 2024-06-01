<?php
namespace App\Jobs;

use App\Services\MlbOddsService;
use App\Events\MlbOddsFetched;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchMlbOdds extends FetchOddsJob implements ShouldQueue
{
use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

protected $mlbOddsService;

public function __construct(MlbOddsService $mlbOddsService)
{
$this->mlbOddsService = $mlbOddsService;
parent::__construct($mlbOddsService, 'baseball_mlb', MlbOddsFetched::class);
}

public function handle()
{
$odds = $this->mlbOddsService->fetchOdds();
event(new MlbOddsFetched($odds));
}
}
