<?php

namespace App\Jobs;

use App\Models\NflEspnEvent;
use App\Models\NflEspnWeek;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchEspnEventsJob implements ShouldQueue
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
        $url = $this->buildUrl($this->seasonYear, $this->seasonType, $this->weekNumber);
        $response = $this->fetchData($url);

        if ($response && $response->successful()) {
            $events = $response->json()['events'] ?? [];

            if (empty($events)) {
                Log::info('No events found for the given parameters.', [
                    'season_year' => $this->seasonYear,
                    'season_type' => $this->seasonType,
                    'week_number' => $this->weekNumber,
                ]);
                return;
            }

            $weekModel = $this->getOrCreateWeek($this->seasonYear, $this->seasonType, $this->weekNumber);
            $this->storeEvents($events, $weekModel);

            Log::info('ESPN NFL events fetched and stored successfully.');
        } else {
            Log::error('Failed to fetch data from ESPN API.', ['url' => $url]);
        }
    }

    protected function buildUrl($seasonYear, $seasonType, $weekNumber): string
    {
        return "https://site.api.espn.com/apis/site/v2/sports/football/nfl/scoreboard?dates={$seasonYear}&seasontype={$seasonType}&week={$weekNumber}";
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

    protected function getOrCreateWeek($seasonYear, $seasonType, $weekNumber)
    {
        return NflEspnWeek::firstOrCreate(
            [
                'season_year' => $seasonYear,
                'season_type' => $seasonType,
                'week_number' => $weekNumber,
            ]
        );
    }

    protected function storeEvents(array $events, $weekModel)
    {
        foreach ($events as $event) {
            $eventData = $this->prepareEventData($event, $weekModel->id);

            $existingEvent = NflEspnEvent::where('week_id', $weekModel->id)
                ->where('event_id', $eventData['event_id'])
                ->first();

            if ($existingEvent) {
                if ($this->hasChanges($existingEvent, $eventData)) {
                    $existingEvent->update($eventData);
                    Log::info("Updated event: {$eventData['name']}");
                } else {
                    Log::info("No changes detected for event: {$eventData['name']}");
                }
            } else {
                NflEspnEvent::create($eventData);
                Log::info("Created new event: {$eventData['name']}");
            }
        }
    }

    protected function prepareEventData(array $event, $weekId): array
    {
        $homeTeam = $event['competitions'][0]['competitors'][0]['team'];
        $awayTeam = $event['competitions'][0]['competitors'][1]['team'];

        return [
            'week_id' => $weekId,
            'event_id' => $event['id'],
            'uid' => $event['uid'],
            'date' => date('Y-m-d H:i:s', strtotime($event['date'])),
            'name' => $event['name'],
            'short_name' => $event['shortName'],
            'attendance' => $event['competitions'][0]['attendance'] ?? null,
            'neutral_site' => $event['competitions'][0]['neutralSite'] ?? null,
            'conference_competition' => $event['competitions'][0]['conferenceCompetition'] ?? null,
            'play_by_play_available' => $event['competitions'][0]['playByPlayAvailable'] ?? null,
            'venue_id' => $event['competitions'][0]['venue']['id'] ?? null,
            'venue_name' => $event['competitions'][0]['venue']['fullName'] ?? null,
            'venue_city' => $event['competitions'][0]['venue']['address']['city'] ?? null,
            'venue_state' => $event['competitions'][0]['venue']['address']['state'] ?? null,
            'venue_indoor' => $event['competitions'][0]['venue']['indoor'] ?? null,
            'status_type_completed' => $event['status']['type']['completed'] ?? null,
            'status_type_detail' => $event['status']['type']['detail'] ?? null,
            'home_team_id' => $homeTeam['id'] ?? null,
            'home_team_score' => $event['competitions'][0]['competitors'][0]['score'] ?? null,
            'home_team_record' => $event['competitions'][0]['competitors'][0]['records'][0]['summary'] ?? null,
            'away_team_id' => $awayTeam['id'] ?? null,
            'away_team_score' => $event['competitions'][0]['competitors'][1]['score'] ?? null,
            'away_team_record' => $event['competitions'][0]['competitors'][1]['records'][0]['summary'] ?? null,
        ];
    }

    protected function hasChanges($existingEvent, array $newData): bool
    {
        foreach ($newData as $key => $value) {
            if ($existingEvent->$key != $value) {
                return true;
            }
        }
        return false;
    }
}
