<?php

namespace App\Console\Commands\Nfl;

use Illuminate\Console\Command;
use App\Services\NFLStatsService;
use App\Services\RosterService;
use App\Models\NflTeam;

class GetNFLTeamRoster extends Command
{
    protected $signature = 'nfl:get-team-roster {teamAbv?}';
    protected $description = 'Get NFL Team Roster';

    protected NFLStatsService $nflStatsService;
    protected RosterService $rosterService;

    public function __construct(NFLStatsService $nflStatsService, RosterService $rosterService)
    {
        parent::__construct();
        $this->nflStatsService = $nflStatsService;
        $this->rosterService = $rosterService;
    }

    public function handle(): void
    {
        $teamAbv = $this->argument('teamAbv');

        if ($teamAbv) {
            $this->processTeam($teamAbv);
        } else {
            $teams = NflTeam::all();
            foreach ($teams as $team) {
                $this->processTeam($team->abbreviation);
            }
        }
    }

    protected function processTeam(string $teamAbv): void
    {
        $this->info('Fetching roster for team: ' . $teamAbv);

        $response = $this->nflStatsService->getNFLTeamRoster($teamAbv);
        $roster = $response['body']['roster'] ?? [];

        if (empty($roster)) {
            $this->error('No roster data found for team: ' . $teamAbv);
            return;
        }

        $this->rosterService->storeRoster($roster, $teamAbv);
    }
}
