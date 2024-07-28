<?php

namespace App\Console\Commands\Nfl;

use Illuminate\Console\Command;
use App\Services\NFLStatsService;
use App\Services\ScheduleService;
use Illuminate\Support\Facades\Log;

class FetchNFLTeamSchedule extends Command
{
    protected $signature = 'fetch:nfl-team-schedule {teamAbv} {season}';
    protected $description = 'Fetch NFL team schedule for a given team and season';

    protected NFLStatsService $nflStatsService;
    protected ScheduleService $scheduleService;

    public function __construct(NFLStatsService $nflStatsService, ScheduleService $scheduleService)
    {
        parent::__construct();
        $this->nflStatsService = $nflStatsService;
        $this->scheduleService = $scheduleService;
    }

    public function handle(): void
    {
        $teamAbv = $this->argument('teamAbv');
        $season = $this->argument('season');

        $this->info('Fetching schedule for team: ' . $teamAbv . ' for season: ' . $season);

        $response = $this->nflStatsService->getNFLTeamSchedule($teamAbv, $season);
        $scheduleData = $response['body']['schedule'] ?? [];

        if (empty($scheduleData)) {
            $this->error('No schedule data found.');
            return;
        }

        Log::info('API Response:', ['response' => $response]);

        $this->scheduleService->storeTeamSchedule($scheduleData);
    }
}
