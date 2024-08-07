<?php

namespace App\Console\Commands\Espn\Events;

use App\Models\NflEspnEvent;
use App\Models\NflEspnWeek;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchEspnEvents extends Command
{
    protected $signature = 'fetch:espn-events {season_year} {season_type} {week_number}';
    protected $description = 'Fetch ESPN NFL events and store them in the database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $seasonYear = $this->argument('season_year');
        $seasonType = $this->argument('season_type');
        $weekNumber = $this->argument('week_number');

        $url = "https://site.api.espn.com/apis/site/v2/sports/football/nfl/scoreboard?dates={$seasonYear}&seasontype={$seasonType}&week={$weekNumber}";
        $response = Http::get($url);

        if ($response->successful()) {
            $events = $response->json()['events'];

            // Ensure the week entry exists in the database
            $weekModel = NflEspnWeek::firstOrCreate(
                [
                    'season_year' => $seasonYear,
                    'season_type' => $seasonType,
                    'week_number' => $weekNumber,
                ]
            );

            foreach ($events as $event) {
                $eventDate = date('Y-m-d H:i:s', strtotime($event['date']));
                $homeTeam = $event['competitions'][0]['competitors'][0]['team'];
                $awayTeam = $event['competitions'][0]['competitors'][1]['team'];

                NflEspnEvent::updateOrCreate(
                    [
                        'week_id' => $weekModel->id,
                        'event_id' => $event['id'],
                    ],
                    [
                        'uid' => $event['uid'],
                        'date' => $eventDate,
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
                    ]
                );
            }

            $this->info('ESPN NFL events fetched and stored successfully.');
        } else {
            $this->error('Failed to fetch data from ESPN API.');
        }
    }
}
