<?php

namespace App\Console\Commands\Espn;

use App\Models\NflEspnEventLog;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FetchNflEventLog extends Command
{
    protected $signature = 'fetch:nfl-event-log {season} {athlete}';
    protected $description = 'Fetch and store NFL event log data from ESPN API';

    protected $client;

    public function __construct()
    {
        parent::__construct();
        $this->client = new Client();
    }

    public function handle()
    {
        $season = $this->argument('season');
        $athlete = $this->argument('athlete');
        $baseUrl = "https://sports.core.api.espn.com/v2/sports/football/leagues/nfl/seasons/{$season}/athletes/{$athlete}/eventlog";

        try {
            $this->info("Fetching data from: $baseUrl");
            $response = $this->client->request('GET', $baseUrl);
            $data = json_decode($response->getBody()->getContents(), true);

            Log::info('API Response:', $data);

            foreach ($data['items'] as $item) {
                $refUrl = $item['$ref'];

                if (preg_match('/\/events\/(\d+)\/competitions\/(\d+)\//', $refUrl, $matches)) {
                    $eventId = $matches[1];
                    $competitionId = $matches[2];

                    $this->fetchAndStoreEventData($refUrl, $athlete, $eventId, $competitionId);
                }
            }

            $this->info('NFL event log data fetched and stored successfully.');
        } catch (Exception $e) {
            Log::error('Error fetching NFL event log data: ' . $e->getMessage());
            $this->error('Failed to fetch and store NFL event log data.');
        }
    }

    private function fetchAndStoreEventData($url, $athleteId, $eventId, $competitionId)
    {
        try {
            $this->info("Fetching event data from: $url");
            $response = $this->client->request('GET', $url);
            $eventData = json_decode($response->getBody()->getContents(), true);

            NflEspnEventLog::updateOrCreate(
                [
                    'athlete_id' => $athleteId,
                    'event_id' => $eventId,
                    'competition_id' => $competitionId,
                ],
                [
                    'data' => json_encode($eventData),
                ]
            );

            $this->info("Event data for athlete ID {$athleteId}, event ID {$eventId}, competition ID {$competitionId} stored successfully.");
        } catch (Exception $e) {
            Log::error('Error fetching event data: ' . $e->getMessage());
            $this->error('Failed to fetch and store event data.');
        }
    }
}
