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
    protected $nflStatsService;

    public function __construct(NFLStatsService $nflStatsService)
    {
        parent::__construct();
        $this->nflStatsService = $nflStatsService;
    }

    public function handle()
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

        // Debugging: Log the structure of the roster
        \Log::info('Roster Data: ' . print_r($roster, true));

        // Store the roster data in the database
        $this->storeRoster($roster, $teamID);
    }

    protected function storeRoster(array $roster, $teamID)
    {
        foreach ($roster as $player) {
            // Debugging: Log each player's data
            \Log::info('Player Data: ' . print_r($player, true));

            if (is_array($player)) {
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

                    if (!empty($player['injury']) && (!empty($player['injury']['description']) || !empty($player['injury']['injDate']) || !empty($player['injury']['designation']))) {
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
            } else {
                $this->error('Invalid player data format.');
            }
        }
    }
}
