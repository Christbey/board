<?php

namespace App\Console\Commands\Espn\Events;

use App\Models\NflEspnEvent;
use App\Models\NflEspnWeek;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class Scoreboard extends Command
{
    protected $signature = 'fetch:nfl-scoreboard {year} {seasontype?} {start_week?} {end_week?}';
    protected $description = 'Fetch and store NFL scoreboard data from ESPN API';

    protected $client;

    public function __construct()
    {
        parent::__construct();
        $this->client = new Client();
    }

    public function handle()
    {
        $year = $this->argument('year');
        $seasontype = $this->argument('seasontype') ?? 2; // Default to regular season (2)
        $startWeek = $this->argument('start_week') ?? 1; // Default to week 1
        $endWeek = $this->argument('end_week') ?? 18; // Default to startWeek if endWeek is not provided

        $baseUrl = 'https://site.api.espn.com/apis/site/v2/sports/football/nfl/scoreboard';

        for ($week = $startWeek; $week <= $endWeek; $week++) {
            $url = "{$baseUrl}?dates={$year}&seasontype={$seasontype}&week={$week}";

            try {
                $this->info("Fetching data from: $url");
                $response = $this->client->request('GET', $url);
                $data = json_decode($response->getBody()->getContents(), true);

                if (empty($data['events'])) {
                    $this->warn("No data found for SeasonType $seasontype Week $week.");
                    continue;
                }

                Log::info('API Response:', $data);

                $weekData = [
                    'season_year' => $data['season']['year'],
                    'season_type' => $data['season']['type'],
                    'week_number' => $data['week']['number'],
                ];

                $weekModel = NflEspnWeek::updateOrCreate($weekData);

                foreach ($data['events'] as $event) {
                    $eventDate = Carbon::parse($event['date'])->toDateTimeString(); // Parse and format the date

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

                $this->info("NFL scoreboard data for SeasonType $seasontype Week $week fetched and stored successfully.");
            } catch (RequestException $e) {
                $this->error("HTTP request failed for SeasonType $seasontype Week $week: " . $e->getMessage());
                Log::error('HTTP Request Error', [
                    'url' => $url,
                    'message' => $e->getMessage(),
                    'response' => $e->hasResponse() ? (string)$e->getResponse()->getBody() : 'No response body',
                ]);
            } catch (Exception $e) {
                $this->error("Failed to fetch and store NFL scoreboard data for SeasonType $seasontype Week $week: " . $e->getMessage());
                Log::error('General Error', [
                    'url' => $url,
                    'message' => $e->getMessage(),
                ]);
            }
        }
    }
}
