<?php

namespace App\Console\Commands\Espn\Teams;

use App\Models\NflEspnAthlete;
use App\Models\NflEspnTeam;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchEspnNflAthletes extends Command
{
    protected $signature = 'espn:fetch-nfl-athletes';
    protected $description = 'Fetch NFL athletes from the ESPN API and store them in the database';

    public function handle()
    {
        $teams = NflEspnTeam::all();

        foreach ($teams as $team) {
            $url = "https://site.api.espn.com/apis/site/v2/sports/football/nfl/teams/{$team->team_id}/roster";

            $response = Http::get($url);

            if ($response->successful()) {
                $athletesGroups = $response->json()['athletes'];

                foreach ($athletesGroups as $group) {
                    $athletes = $group['items'];

                    foreach ($athletes as $athlete) {
                        // Make an additional request to get detailed athlete information if needed
                        if (!isset($athlete['debutYear'])) {
                            $athleteDetailUrl = "https://www.espn.com/nfl/player/_/id/{$athlete['id']}";
                            $athleteDetailResponse = Http::get($athleteDetailUrl);
                            if ($athleteDetailResponse->successful()) {
                                $athleteDetails = $athleteDetailResponse->json();
                                $athlete['debutYear'] = $athleteDetails['debutYear'] ?? null;
                            }
                        }

                        $status = null;
                        if (isset($athlete['status'])) {
                            $status = is_array($athlete['status']) ? json_encode($athlete['status']) : $athlete['status'];
                        }

                        NflEspnAthlete::updateOrCreate(
                            ['athlete_id' => $athlete['id']],
                            [
                                'team_id' => $team->team_id,
                                'season_year' => 2024, // Update accordingly
                                'uid' => $athlete['uid'],
                                'guid' => $athlete['guid'],
                                'first_name' => $athlete['firstName'],
                                'last_name' => $athlete['lastName'],
                                'full_name' => $athlete['fullName'],
                                'display_name' => $athlete['displayName'],
                                'short_name' => $athlete['shortName'],
                                'weight' => $athlete['weight'] ?? null,
                                'display_weight' => $athlete['displayWeight'] ?? null,
                                'height' => $athlete['height'] ?? null,
                                'display_height' => $athlete['displayHeight'] ?? null,
                                'age' => $athlete['age'] ?? null,
                                'date_of_birth' => isset($athlete['dateOfBirth']) ? date('Y-m-d', strtotime($athlete['dateOfBirth'])) : null,
                                'debut_year' => $athlete['debutYear'] ?? null,
                                'position' => $athlete['position']['displayName'] ?? null,
                                'status' => $status,
                                'jersey' => $athlete['jersey'] ?? null,
                            ]
                        );
                    }
                }

                $this->info("Athletes data for team {$team->display_name} has been fetched and stored successfully.");
            } else {
                $this->error("Failed to fetch athletes data for team {$team->display_name}.");
            }
        }

        return 0;
    }
}

// Works as Expected