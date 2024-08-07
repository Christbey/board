<?php

namespace App\Console\Commands\Espn\Athletes;

use App\Models\EspnNflDepthChart;
use App\Models\NflEspnAthlete;
use App\Models\NflEspnTeam;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class DepthChart extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'espn:depth-chart {team_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch NFL player depth chart from the ESPN API';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $teamId = $this->argument('team_id');

        if ($teamId) {
            $this->fetchDepthChart($teamId);
        } else {
            $teams = NflEspnTeam::all();

            foreach ($teams as $team) {
                $this->fetchDepthChart($team->team_id);
            }
        }

        return Command::SUCCESS;
    }

    private function fetchDepthChart($teamId)
    {
        $url = "https://sports.core.api.espn.com/v2/sports/football/leagues/nfl/seasons/2024/teams/{$teamId}/depthcharts";

        // Send a GET request to the endpoint
        $response = Http::get($url);

        if ($response->successful()) {
            $data = $response->json();

            // Extract depth chart information
            $items = $data['items'];
            foreach ($items as $item) {
                $this->info("Depth Chart ID: {$item['id']}, Name: {$item['name']}");
                $positions = $item['positions'];
                foreach ($positions as $positionKey => $position) {
                    $this->info("Position: {$position['position']['displayName']}");
                    foreach ($position['athletes'] as $athlete) {
                        $athleteDetails = Http::get($athlete['athlete']['$ref'])->json();
                        $jersey = $athleteDetails['jersey'] ?? null;
                        $this->info("  - Name: {$athleteDetails['fullName']}, Jersey: {$jersey}, Position: {$athleteDetails['position']['name']}, Rank: {$athlete['rank']}");

                        // Format the date_of_birth if it's present
                        $dateOfBirth = isset($athleteDetails['dateOfBirth']) ? date('Y-m-d', strtotime($athleteDetails['dateOfBirth'])) : null;

                        // Check if the 'status' key exists
                        $status = isset($athleteDetails['status']) ? (is_array($athleteDetails['status']) ? json_encode($athleteDetails['status']) : $athleteDetails['status']) : null;

                        // Ensure the athlete exists in the database
                        NflEspnAthlete::updateOrCreate(
                            [
                                'athlete_id' => $athleteDetails['id'],
                            ],
                            [
                                'team_id' => $teamId,
                                'season_year' => 2024,
                                'uid' => $athleteDetails['uid'] ?? null,
                                'guid' => $athleteDetails['guid'] ?? null,
                                'first_name' => $athleteDetails['firstName'] ?? null,
                                'last_name' => $athleteDetails['lastName'] ?? null,
                                'full_name' => $athleteDetails['fullName'] ?? null,
                                'display_name' => $athleteDetails['displayName'] ?? null,
                                'short_name' => $athleteDetails['shortName'] ?? null,
                                'weight' => $athleteDetails['weight'] ?? null,
                                'display_weight' => $athleteDetails['displayWeight'] ?? null,
                                'height' => $athleteDetails['height'] ?? null,
                                'display_height' => $athleteDetails['displayHeight'] ?? null,
                                'age' => $athleteDetails['age'] ?? null,
                                'date_of_birth' => $dateOfBirth,
                                'debut_year' => $athleteDetails['debutYear'] ?? null,
                                'position' => $athleteDetails['position']['name'] ?? null,
                                'status' => $status,
                                'jersey' => $athleteDetails['jersey'] ?? null, // Ensure jersey is saved
                            ]
                        );

                        // Save the depth chart data to the database
                        EspnNflDepthChart::updateOrCreate(
                            [
                                'team_id' => $teamId,
                                'athlete_id' => $athleteDetails['id'],
                                'position' => $athleteDetails['position']['name'],
                            ],
                            [
                                'depth' => $athlete['rank'],
                            ]
                        );
                    }
                }
            }
        } else {
            $this->error('Failed to retrieve data for team_id: ' . $teamId . ' - ' . $response->status());
        }
    }
}
