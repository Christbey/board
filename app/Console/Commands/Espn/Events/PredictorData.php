<?php

namespace App\Console\Commands\Espn\Events;

use App\Models\NflEspnEvent;
use App\Models\NflEspnEventPredictor;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class PredictorData extends Command
{
    protected $signature = 'fetch:espn-predictor {event_id?}';
    protected $description = 'Fetch ESPN predictor data for each event or a specific event and log it in the terminal';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $eventId = $this->argument('event_id');

        if ($eventId) {
            // Fetch and process data for the specific event ID
            $this->fetchAndProcessEvent($eventId);
        } else {
            // Retrieve all event IDs from the nfl_espn_events table
            $events = NflEspnEvent::all('event_id');

            foreach ($events as $event) {
                $this->fetchAndProcessEvent($event->event_id);
            }
        }
    }

    private function fetchAndProcessEvent($eventId)
    {
        $url = "https://sports.core.api.espn.com/v2/sports/football/leagues/nfl/events/{$eventId}/competitions/{$eventId}/predictor?lang=en&region=us";

        $response = Http::get($url);

        if ($response->successful()) {
            $data = $response->json();

            $homeTeamUrl = $data['homeTeam']['team']['$ref'];
            $awayTeamUrl = $data['awayTeam']['team']['$ref'];

            $homeTeamId = $this->getTeamIdFromUrl($homeTeamUrl);
            $awayTeamId = $this->getTeamIdFromUrl($awayTeamUrl);

            // Convert the last_modified to the correct format
            $lastModified = Carbon::parse($data['lastModified'])->toDateTimeString();

            $predictorData = [
                'event_id' => $eventId,
                'name' => $data['name'],
                'short_name' => $data['shortName'],
                'last_modified' => $lastModified,
                'home_team_id' => $homeTeamId,
                'away_team_id' => $awayTeamId,
            ];

            foreach ($data['homeTeam']['statistics'] as $stat) {
                $predictorData['home_' . $stat['name']] = $stat['value'];
            }

            foreach ($data['awayTeam']['statistics'] as $stat) {
                $predictorData['away_' . $stat['name']] = $stat['value'];
            }

            // Log the data to the terminal for testing
            $this->info(print_r($predictorData, true));

            // Save the data using the model
            NflEspnEventPredictor::updateOrCreate(
                ['event_id' => $eventId], // Update existing record or create a new one
                $predictorData
            );

            $this->info('Predictor data fetched and stored successfully for event ID: ' . $eventId);
        } else {
            $this->error('Failed to fetch data for event ID: ' . $eventId);
        }
    }

    private function getTeamIdFromUrl($url)
    {
        $parts = explode('/', parse_url($url, PHP_URL_PATH));
        return end($parts);
    }
}
