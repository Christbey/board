<?php

namespace App\Jobs\Nfl;

use App\Services\Nfl\ScheduleService;
use App\Services\Nfl\Stats\NFLStatsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchNFLTeamScheduleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $teamAbv;
    protected $season;

    public function __construct($teamAbv, $season)
    {
        $this->teamAbv = $teamAbv;
        $this->season = $season;
    }

    public function handle(NFLStatsService $nflStatsService, ScheduleService $scheduleService)
    {
        $response = $nflStatsService->getNFLTeamSchedule($this->teamAbv, $this->season);
        $scheduleData = $response['body']['schedule'] ?? [];

        if (!empty($scheduleData)) {
            $scheduleService->storeTeamSchedule($scheduleData);
        }
    }
}
