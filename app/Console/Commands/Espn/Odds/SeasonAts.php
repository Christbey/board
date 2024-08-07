<?php

namespace App\Console\Commands\Espn\Odds;

use App\Models\NflEspnAtsRecord;
use App\Models\NflEspnTeam;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SeasonAts extends Command
{
    protected $signature = 'espn:ats-record {season} {team_id?}';
    protected $description = 'Fetch NFL ATS records from ESPN API';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $season = $this->argument('season');
        $teamId = $this->argument('team_id');

        if ($teamId) {
            $this->fetchAndStoreAtsRecords($teamId, $season);
        } else {
            $teams = NflEspnTeam::all();
            foreach ($teams as $team) {
                $this->fetchAndStoreAtsRecords($team->team_id, $season);
            }
        }
    }

    protected function fetchAndStoreAtsRecords($teamId, $season)
    {
        $url = "https://sports.core.api.espn.com/v2/sports/football/leagues/nfl/seasons/{$season}/types/2/teams/{$teamId}/ats";
        $response = Http::get($url);

        if ($response->successful()) {
            $data = $response->json();

            if (isset($data['items']) && count($data['items']) > 0) {
                foreach ($data['items'] as $item) {
                    NflEspnAtsRecord::updateOrCreate(
                        [
                            'team_id' => $teamId,
                            'season' => $season,
                            'type_id' => $item['type']['id'],
                        ],
                        [
                            'type_name' => $item['type']['name'],
                            'type_description' => $item['type']['description'],
                            'wins' => $item['wins'],
                            'losses' => $item['losses'],
                            'pushes' => $item['pushes'],
                        ]
                    );
                }

                $this->info("NFL ATS records for team {$teamId} in season {$season} fetched and stored successfully.");
            } else {
                $this->error("No ATS records data found for team {$teamId} in season {$season}.");
            }
        } else {
            $this->error("Failed to fetch NFL ATS records for team {$teamId} in season {$season}.");
        }
    }
}
