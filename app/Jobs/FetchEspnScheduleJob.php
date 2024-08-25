<?php

namespace App\Jobs;

use App\Models\NflEspnEvent;
use App\Models\NflEspnEventOdd;
use App\Models\NflEspnWeek;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchEspnScheduleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $seasonYear;
    protected $seasonType;
    protected $weekNumber;

    public function __construct($seasonYear, $seasonType, $weekNumber)
    {
        $this->seasonYear = $seasonYear;
        $this->seasonType = $seasonType;
        $this->weekNumber = $weekNumber;
    }

    public function handle()
    {
        $url = $this->buildUrl();
        $response = $this->fetchData($url);

        if ($response && $response->successful()) {
            $events = $response->json()['events'] ?? [];

            if (empty($events)) {
                Log::info('No events found for the given parameters.', $this->logParams());
                return;
            }

            $weekModel = $this->getOrCreateWeek();
            $this->processEvents($events, $weekModel);

            Log::info('ESPN NFL events fetched and stored successfully.');
        } else {
            Log::error('Failed to fetch data from ESPN API.', ['url' => $url]);
        }
    }

    protected function buildUrl(): string
    {
        return "https://site.api.espn.com/apis/site/v2/sports/football/nfl/scoreboard?dates={$this->seasonYear}&seasontype={$this->seasonType}&week={$this->weekNumber}";
    }

    protected function fetchData(string $url)
    {
        try {
            return Http::get($url);
        } catch (Exception $e) {
            Log::error('Error fetching ESPN events', ['url' => $url, 'error' => $e->getMessage()]);
            return null;
        }
    }

    protected function getOrCreateWeek()
    {
        return NflEspnWeek::firstOrCreate([
            'season_year' => $this->seasonYear,
            'season_type' => $this->seasonType,
            'week_number' => $this->weekNumber,
        ]);
    }

    protected function processEvents(array $events, $weekModel)
    {
        foreach ($events as $event) {
            $eventData = $this->prepareEventData($event, $weekModel->id);

            $existingEvent = NflEspnEvent::updateOrCreate(
                ['week_id' => $weekModel->id, 'event_id' => $eventData['event_id']],
                $eventData
            );

            if ($existingEvent->wasChanged()) {
                Log::info("Event updated: {$eventData['name']}");
            } else {
                Log::info("No changes detected for event: {$eventData['name']}");
            }

            // Fetch and store additional competition details
            $this->fetchCompetitionDetails($event['id']);

            // Fetch and store odds data if available
            
        }
    }

    protected function prepareEventData(array $event, $weekId): array
    {
        $competition = $event['competitions'][0];
        $homeTeam = $competition['competitors'][0]['team'];
        $awayTeam = $competition['competitors'][1]['team'];

        return [
            'week_id' => $weekId,
            'event_id' => $event['id'],
            'uid' => $event['uid'],
            'date' => date('Y-m-d H:i:s', strtotime($event['date'])),
            'name' => $event['name'],
            'short_name' => $event['shortName'],
            'attendance' => $competition['attendance'] ?? null,
            'neutral_site' => $competition['neutralSite'] ?? null,
            'conference_competition' => $competition['conferenceCompetition'] ?? null,
            'play_by_play_available' => $competition['playByPlayAvailable'] ?? null,
            'venue_id' => $competition['venue']['id'] ?? null,
            'venue_name' => $competition['venue']['fullName'] ?? null,
            'venue_city' => $competition['venue']['address']['city'] ?? null,
            'venue_state' => $competition['venue']['address']['state'] ?? null,
            'venue_indoor' => $competition['venue']['indoor'] ?? null,
            'status_type_completed' => $event['status']['type']['completed'] ?? null,
            'status_type_detail' => $event['status']['type']['detail'] ?? null,
            'home_team_id' => $homeTeam['id'] ?? null,
            'home_team_score' => $competition['competitors'][0]['score'] ?? null,
            'home_team_record' => $competition['competitors'][0]['records'][0]['summary'] ?? null,
            'away_team_id' => $awayTeam['id'] ?? null,
            'away_team_score' => $competition['competitors'][1]['score'] ?? null,
            'away_team_record' => $competition['competitors'][1]['records'][0]['summary'] ?? null,
        ];
    }

    protected function fetchCompetitionDetails($eventId)
    {
        $url = "https://sports.core.api.espn.com/v2/sports/football/leagues/nfl/events/{$eventId}/competitions/{$eventId}?lang=en&region=us";
        $response = $this->fetchData($url);

        if ($response && $response->successful()) {
            $competitionData = $response->json();

            Log::info("Fetched competition details for event ID: {$eventId}");

            if (isset($competitionData['weather'])) {
                $this->storeWeatherData($competitionData['weather'], $eventId);
            }
        } else {
            Log::error('Failed to fetch competition details from ESPN API.', ['url' => $url]);
        }
    }

    protected function storeWeatherData(array $weatherData, $eventId)
    {
        // DB::listen(function ($query) {
        //     Log::info($query->sql, $query->bindings);
        // });

        $data = [
            'weather_wind_direction' => $weatherData['windDirection'] ?? null,
            'weather_display_value' => $weatherData['displayValue'] ?? null,
            'weather_precipitation' => $weatherData['precipitation'] ?? null,
            'weather_temperature' => $weatherData['temperature'] ?? null,
            'weather_low_temperature' => $weatherData['lowTemperature'] ?? null,
            'weather_last_updated' => isset($weatherData['lastUpdated']) ? date('Y-m-d H:i:s', strtotime($weatherData['lastUpdated'])) : null,
            'weather_condition_id' => $weatherData['conditionId'] ?? null,
            'weather_wind_speed' => $weatherData['windSpeed'] ?? null,
            'weather_zip_code' => $weatherData['zipCode'] ?? null,
            'weather_type' => $weatherData['type'] ?? null,
            'weather_gust' => $weatherData['gust'] ?? null,
            'weather_link_href' => $weatherData['link']['href'] ?? null,
            'weather_link_short_text' => $weatherData['link']['shortText'] ?? null,
            'weather_high_temperature' => $weatherData['highTemperature'] ?? null,
        ];

        $existingEvent = NflEspnEvent::where('event_id', $eventId)->first();

        if ($existingEvent) {
            $existingEvent->update($data);
            Log::info("Updated weather data for event ID: {$eventId}");
        } else {
            Log::error("No event found for event ID: {$eventId} to update weather data.");
        }
    }


    private function logParams(): array
    {
        return [
            'season_year' => $this->seasonYear,
            'season_type' => $this->seasonType,
            'week_number' => $this->weekNumber,
        ];
    }
}
