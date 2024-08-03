<?php

namespace App\Console\Commands\Espn\Teams;

use App\Models\NflEspnInjury;
use App\Models\NflEspnTeam;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchEspnNflInjuries extends Command
{
    protected $signature = 'espn:fetch-nfl-injuries {team_id?}';
    protected $description = 'Fetch NFL injuries from the ESPN API and store them in the database';

    public function handle()
    {
        $teamId = $this->argument('team_id');
        $teams = $teamId ? NflEspnTeam::where('team_id', $teamId)->get() : NflEspnTeam::all();

        foreach ($teams as $team) {
            $url = "https://sports.core.api.espn.com/v2/sports/football/leagues/nfl/teams/{$team->team_id}/injuries";

            $response = Http::get($url);

            if ($response->successful()) {
                $injuries = $response->json()['items'];

                foreach ($injuries as $injuryRef) {
                    $injuryUrl = $injuryRef['$ref'];
                    $injuryResponse = Http::get($injuryUrl);

                    if ($injuryResponse->successful()) {
                        $injury = $injuryResponse->json();

                        // Fetch athlete details from the $ref link
                        $athleteUrl = $injury['athlete']['$ref'];
                        $athleteResponse = Http::get($athleteUrl);
                        $athleteId = null;

                        if ($athleteResponse->successful()) {
                            $athlete = $athleteResponse->json();
                            $athleteId = $athlete['id'];
                        }

                        NflEspnInjury::updateOrCreate(
                            ['injury_id' => $injury['id']],
                            [
                                'team_id' => $team->team_id,
                                'athlete_id' => $athleteId,
                                'type' => $injury['type']['description'] ?? null,
                                'status' => $injury['status'] ?? null,
                                'date' => isset($injury['date']) ? date('Y-m-d', strtotime($injury['date'])) : null,
                                'description' => $injury['shortComment'] ?? null,
                            ]
                        );
                    } else {
                        $this->error("Failed to fetch data for injury URL: $injuryUrl");
                    }
                }

                $this->info("Injuries data for team {$team->display_name} has been fetched and stored successfully.");
            } else {
                $this->error("Failed to fetch injuries data for team {$team->display_name}.");
            }
        }

        return 0;
    }

}
// Works as Expected