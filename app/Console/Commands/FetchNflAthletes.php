<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Models\NflEspnAthlete;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

class FetchNflAthletes extends Command
{
    protected $signature = 'fetch:nfl-athletes {year} {team_id}';
    protected $description = 'Fetch and store NFL athletes data from ESPN API';

    protected $client;

    public function __construct()
    {
        parent::__construct();
        $this->client = new Client();
    }

    public function handle()
    {
        $year = $this->argument('year');
        $team_id = $this->argument('team_id');
        $baseUrl = 'https://sports.core.api.espn.com/v2/sports/football/leagues/nfl/seasons';
        $url = "{$baseUrl}/{$year}/teams/{$team_id}/athletes?limit=200";

        $this->fetchAndStoreAthleteData($url, $year, $team_id);

        $this->info('NFL athletes data fetched and stored successfully.');
    }

    private function fetchAndStoreAthleteData($url, $year, $team_id)
    {
        $response = $this->client->request('GET', $url);
        $athleteDataList = json_decode($response->getBody()->getContents(), true);

        foreach ($athleteDataList['items'] as $item) {
            $athleteUrl = $item['$ref'];
            $athleteResponse = $this->client->request('GET', $athleteUrl);
            $athleteData = json_decode($athleteResponse->getBody()->getContents(), true);

            $athleteId = $athleteData['id'];

            $dateOfBirth = isset($athleteData['dateOfBirth']) ? Carbon::parse($athleteData['dateOfBirth'])->toDateString() : null;

            NflEspnAthlete::updateOrCreate(
                ['athlete_id' => $athleteId],
                [
                    'team_id' => $team_id,
                    'season_year' => $year,
                    'uid' => $athleteData['uid'] ?? null,
                    'guid' => $athleteData['guid'] ?? null,
                    'first_name' => $athleteData['firstName'] ?? null,
                    'last_name' => $athleteData['lastName'] ?? null,
                    'full_name' => $athleteData['fullName'] ?? null,
                    'display_name' => $athleteData['displayName'] ?? null,
                    'short_name' => $athleteData['shortName'] ?? null,
                    'weight' => $athleteData['weight'] ?? null,
                    'display_weight' => $athleteData['displayWeight'] ?? null,
                    'height' => $athleteData['height'] ?? null,
                    'display_height' => $athleteData['displayHeight'] ?? null,
                    'age' => $athleteData['age'] ?? null,
                    'date_of_birth' => $dateOfBirth,
                    'debut_year' => $athleteData['debutYear'] ?? null,
                    'position' => $athleteData['position']['displayName'] ?? null,
                    'status' => $athleteData['status']['name'] ?? null,
                ]
            );
        }
    }
}
