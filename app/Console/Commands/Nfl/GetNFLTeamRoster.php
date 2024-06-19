<?php

namespace App\Console\Commands\Nfl;

use Illuminate\Console\Command;
use App\Services\NFLStatsService;
use App\Traits\StoresPlayerData;
class GetNFLTeamRoster extends Command
{
    use StoresPlayerData;  // Use the trait here

    protected $signature = 'nfl:get-team-roster {teamAbv}';
    protected $description = 'Get NFL Team Roster';
    protected NFLStatsService $nflStatsService;

    public function __construct(NFLStatsService $nflStatsService)
    {
        parent::__construct();
        $this->nflStatsService = $nflStatsService;
    }

    public function handle(): void
    {
        $teamAbv = $this->argument('teamAbv');
        $this->info('Fetching roster for team: ' . $teamAbv);

        // Fetch the team roster
        $response = $this->nflStatsService->getNFLTeamRoster($teamAbv);
        $roster = $response['body']['roster'] ?? [];

        if (empty($roster)) {
            $this->error('No roster data found.');
            return;
        }
        $this->storeRoster($roster);
    }
    protected function storeRoster(array $roster): void
    {
        foreach ($roster as $player) {
            if (!is_array($player)) {
                $this->error('Invalid player data format.');
                continue;
            }
            $teamAbv = $this->argument('teamAbv');

            $this->storePlayerData($player);
        }

        $this->info(  $teamAbv . 'roster has been saved successfully.');
    }
}
