<?php

namespace App\Console\Commands\Nfl;

use Illuminate\Console\Command;
use App\Services\NFLStatsService;
use App\Traits\StoresPlayerData;
use App\Models\NflInjury;
use App\Models\NflTeam;
use Carbon\Carbon;

class GetNFLTeamRoster extends Command
{
    use StoresPlayerData;

    protected $signature = 'nfl:get-team-roster {teamAbv?}'; // Make teamAbv optional
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

        if ($teamAbv) {
            // Fetch and process roster for a specific team
            $this->processTeam($teamAbv);
        } else {
            // Fetch and process rosters for all teams
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

        $this->storeRoster($roster, $teamAbv);
    }

    protected function storeRoster(array $roster, string $teamAbv): void
    {
        foreach ($roster as $player) {
            if (!is_array($player)) {
                $this->error('Invalid player data format for team: ' . $teamAbv);
                continue;
            }

            $this->storePlayerData($player);
            $this->storeInjuryData($player);
        }

        $this->info($teamAbv . ' roster has been saved successfully.');
    }

    protected function storeInjuryData(array $player): void
    {
        $injury = $player['injury'];

        if (empty($injury['description']) || empty($injury['designation'])) {
            return;
        }

        // Extract injury type from description
        preg_match('/\(([^)]+)\)/', $injury['description'], $matches);
        $injuryType = $matches[1] ?? null;

        if ($injuryType === null) {
            return;
        }

        // Extract injury date from the description
        preg_match('/(\w+\s\d{1,2}):/', $injury['description'], $dateMatches);
        $injuryDate = isset($dateMatches[1]) ? Carbon::parse($dateMatches[1]) : null;

        if ($injuryDate === null) {
            return;
        }

        NflInjury::updateOrCreate(
            [
                'player_id' => $player['playerID'],
                'team_id' => $player['teamID']
            ],
            [
                'injury_type' => $injuryType,
                'injury_date' => $injuryDate,
                'designation' => $injury['designation'],
                'description' => $injury['description'],
            ]
        );
    }
}
