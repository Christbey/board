<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NFLStatsService;
use App\Models\NflPlayer;
use App\Models\NflInjury;

class GetNFLTeamRoster extends Command
{
    protected $signature = 'nfl:get-team-roster {teamID} {teamAbv}';
    protected $description = 'Get NFL Team Roster';
    protected NFLStatsService $nflStatsService;

    public function __construct(NFLStatsService $nflStatsService)
    {
        parent::__construct();
        $this->nflStatsService = $nflStatsService;
    }

    public function handle(): void
    {
        $teamID = $this->argument('teamID');
        $teamAbv = $this->argument('teamAbv');

        $this->info('Fetching roster for team: ' . $teamAbv);

        $response = $this->nflStatsService->getNFLTeamRoster($teamID, $teamAbv);
        $roster = $response['body']['roster'] ?? [];

        if (empty($roster)) {
            $this->error('No roster data found.');
            return;
        }

        $this->storeRoster($roster, $teamID);
    }

    protected function storeRoster(array $roster, $teamID): void
    {
        foreach ($roster as $player) {
            if (!is_array($player)) {
                $this->error('Invalid player data format.');
                continue;
            }

            $playerData = [
                'player_id' => $player['playerID'] ?? null,
                'longName' => $player['longName'] ?? null,
                'team' => $player['team'] ?? null,
                'jerseyNum' => $player['jerseyNum'] ?? null,
                'pos' => $player['pos'] ?? null,
                'exp' => $player['exp'] ?? null,
                'school' => $player['school'] ?? null,
                'age' => $player['age'] ?? null,
                'height' => $player['height'] ?? null,
                'weight' => $player['weight'] ?? null,
            ];

            if ($playerData['player_id'] !== null) {
                NflPlayer::updateOrCreate(
                    ['player_id' => $player['playerID']],
                    $playerData
                );

                if (!empty($player['injury']) && $this->hasInjuryData($player['injury'])) {
                    $injuryData = [
                        'player_id' => $player['playerID'],
                        'team_id' => $teamID,
                        'injury_type' => $player['injury']['description'] ?? null,
                        'injury_date' => !empty($player['injury']['injDate']) ? $player['injury']['injDate'] : null,
                        'designation' => $player['injury']['designation'] ?? null,
                    ];

                    NflInjury::updateOrCreate(
                        ['player_id' => $player['playerID']],
                        $injuryData
                    );
                }

                $this->info('Player data stored for: ' . $player['longName']);
            }
        }
    }

    protected function hasInjuryData(array $injury): bool
    {
        return !empty($injury['description']) || !empty($injury['injDate']) || !empty($injury['designation']);
    }
}
