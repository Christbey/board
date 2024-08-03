<?php

namespace App\Console\Commands\Espn;

use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EspnTeamDetails extends Command
{
    protected $signature = 'espn:team-details {team_id} {year=2023}';
    protected $description = 'Fetch NFL team details from the ESPN API';

    public function handle()
    {
        $team_id = $this->argument('team_id');
        $year = $this->argument('year');
        $url = "https://sports.core.api.espn.com/v2/sports/football/leagues/nfl/seasons/{$year}/teams/{$team_id}";

        try {
            $response = Http::timeout(60)->retry(3, 100)->get($url);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('Fetched team data', $data);

                $fetchDataFromUrl = function ($url) {
                    $response = Http::get($url);
                    if ($response->successful()) {
                        return $response->json();
                    }
                    Log::error("Failed to retrieve data from $url");
                    return ['error' => "Failed to retrieve data from $url"];
                };

                $additionalEndpoints = [
                    'injuries' => $data['injuries']['$ref'] ?? null,
                    'notes' => $data['notes']['$ref'] ?? null,
                    'againstTheSpreadRecords' => $data['againstTheSpreadRecords']['$ref'] ?? null,
                    'franchise' => $data['franchise']['$ref'] ?? null,
                    'events' => $data['events']['$ref'] ?? null,
                    'transactions' => $data['transactions']['$ref'] ?? null,
                    'coaches' => $data['coaches']['$ref'] ?? null,
                    'attendance' => $data['attendance']['$ref'] ?? null,
                ];

                foreach ($additionalEndpoints as $key => $endpoint) {
                    if ($endpoint) {
                        Log::info("Fetching $key data from: $endpoint");
                        $data[$key] = $fetchDataFromUrl($endpoint);

                        if (in_array($key, ['events', 'injuries']) && isset($data[$key]['items'])) {
                            foreach ($data[$key]['items'] as &$item) {
                                if (isset($item['$ref'])) {
                                    $itemUrl = htmlspecialchars_decode($item['$ref'], ENT_QUOTES);
                                    Log::info("Fetching item data from: $itemUrl");
                                    $item = $fetchDataFromUrl($itemUrl);

                                    if ($key == 'events' && isset($item['competitions'][0]['predictor']['$ref'])) {
                                        $predictorUrl = htmlspecialchars_decode($item['competitions'][0]['predictor']['$ref'], ENT_QUOTES);
                                        Log::info("Fetching predictor data from: $predictorUrl");
                                        $item['predictor'] = $fetchDataFromUrl($predictorUrl);
                                    }
                                }
                            }
                        }

                        if ($key == 'coaches' && isset($data['coaches']['items'])) {
                            foreach ($data['coaches']['items'] as &$coach) {
                                if (isset($coach['$ref'])) {
                                    $coachUrl = htmlspecialchars_decode($coach['$ref'], ENT_QUOTES);
                                    Log::info("Fetching coach data from: $coachUrl");
                                    $coach = $fetchDataFromUrl($coachUrl);
                                }
                            }
                        }
                    } else {
                        $data[$key] = ['error' => "$key data not available."];
                    }
                }

                Log::info('Complete team data with additional info', $data);
                $this->output->writeln(json_encode($data, JSON_PRETTY_PRINT));

                return Command::SUCCESS;
            } else {
                $this->error('Failed to retrieve data: ' . $response->status());
                Log::error('Failed to retrieve team data', ['status' => $response->status()]);
                return Command::FAILURE;
            }
        } catch (ConnectionException $e) {
            $this->error('Connection error: ' . $e->getMessage());
            Log::error('Connection error while retrieving team data: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
