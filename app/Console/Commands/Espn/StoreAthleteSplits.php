<?php

namespace App\Console\Commands\Espn;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

class StoreAthleteSplits extends Command
{
    protected $signature = 'store:athlete-splits {event_id}';
    protected $description = 'Store athlete splits from ESPN API';

    public function __construct()
    {
        parent::__construct();
        // Set the memory limit
        ini_set('memory_limit', '1024M'); // You can adjust the memory limit as needed
    }

    public function handle()
    {
        $eventId = $this->argument('event_id');
        $eventUrl = "http://sports.core.api.espn.com/v2/sports/football/leagues/nfl/events/{$eventId}/competitions/{$eventId}?lang=en&region=us";
        $eventResponse = Http::get($eventUrl);

        if ($eventResponse->successful()) {
            $eventData = $eventResponse->json();
            $competitors = $eventData['competitors'] ?? [];
            $seasonYear = date('Y', strtotime($eventData['date'])); // Extract season year from date

            foreach ($competitors as $competitor) {
                $teamId = $competitor['id'];
                $homeAway = $competitor['homeAway']; // Get the homeAway value
                $statisticsUrl = "https://sports.core.api.espn.com/v2/sports/football/leagues/nfl/events/{$eventId}/competitions/{$eventId}/competitors/{$teamId}/statistics";
                $statisticsResponse = Http::get($statisticsUrl);

                if ($statisticsResponse->successful()) {
                    $statisticsData = $statisticsResponse->json();
                    $this->processStatistics($statisticsData, $teamId, $homeAway, $competitors, $seasonYear);
                } else {
                    $this->error("Failed to fetch statistics for team ID: {$teamId}");
                }
            }

            $this->info('All athlete splits data has been stored successfully.');
        } else {
            $this->error('Failed to fetch event data from the API.');
        }
    }

    protected function processStatistics($statisticsData, $teamId, $homeAway, $competitors, $seasonYear)
    {
        $awayTeamId = $this->extractTeamIdFromCompetitors($competitors, 'away');
        $homeTeamId = $this->extractTeamIdFromCompetitors($competitors, 'home');

        foreach ($statisticsData['splits']['categories'] as $category) {
            $categoryName = $category['name'] ?? 'N/A';
            foreach ($category['athletes'] as $athlete) {
                $athleteUrl = $athlete['athlete']['$ref'];
                $athleteResponse = Http::get($athleteUrl);

                if ($athleteResponse->successful()) {
                    $athleteData = $athleteResponse->json();
                    $athleteId = $athleteData['id'];

                    // Determine the team_id based on the competitor's homeAway value
                    $teamId = $homeAway === 'home' ? $homeTeamId : $awayTeamId;

                    // Check if athlete exists in the database
                    $athleteExists = DB::table('nfl_espn_athletes')->where('athlete_id', $athleteId)->exists();

                    if (!$athleteExists) {
                        // Insert the athlete into the nfl_espn_athletes table
                        DB::table('nfl_espn_athletes')->insert([
                            'athlete_id' => $athleteId,
                            'team_id' => $teamId,
                            'season_year' => $seasonYear,
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
                            'date_of_birth' => $athleteData['dateOfBirth'] ?? null,
                            'debut_year' => $athleteData['debutYear'] ?? null,
                            'position' => $athleteData['position'] ?? null,
                            'status' => $athleteData['status'] ?? null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $this->info("Athlete ID: {$athleteId} added to the database.");
                    }

                    $statisticsUrl = $athlete['statistics']['$ref'];
                    $statisticsResponse = Http::get($statisticsUrl);
                    if ($statisticsResponse->successful()) {
                        $statistics = $statisticsResponse->json();
                        $this->storeAthleteData($statistics, $athleteId, $teamId, $awayTeamId, $homeTeamId, $categoryName);
                    } else {
                        $this->error("Failed to fetch statistics for athlete ID: {$athleteId}");
                    }
                } else {
                    $this->error("Failed to fetch data for athlete URL: {$athleteUrl}");
                }
            }
        }
    }

    protected function storeAthleteData($data, $athleteId, $teamId, $awayTeamId, $homeTeamId, $categoryName)
    {
        $tableColumns = Schema::getColumnListing('nfl_espn_athlete_splits');

        $insertData = [
            'athlete_id' => $athleteId,
            'split_id' => $data['splits']['id'] ?? 0,
            'split_name' => $data['splits']['name'] ?? 'N/A',
            'split_abbreviation' => $data['splits']['abbreviation'] ?? 'N/A',
            'away_team_id' => $awayTeamId,
            'home_team_id' => $homeTeamId,
            'category_name' => $categoryName,
        ];

        foreach ($data['splits']['categories'] as $category) {
            $categoryName = $category['name'];
            foreach ($category['stats'] as $stat) {
                $statName = "{$categoryName}_{$stat['name']}";
                $statValue = $stat['value'] ?? null;

                // Only add the stat if the column exists in the table
                if (in_array($statName, $tableColumns)) {
                    $insertData[$statName] = $statValue;
                }
            }
        }

        // Insert or update the athlete split data
        DB::table('nfl_espn_athlete_splits')->updateOrInsert(
            [
                'athlete_id' => $athleteId,
                'split_id' => $data['splits']['id'] ?? 0,
            ],
            $insertData
        );

        $this->info('Athlete splits data has been stored successfully.');
    }

    protected function extractTeamIdFromCompetitors($competitors, $type)
    {
        foreach ($competitors as $competitor) {
            if ($competitor['homeAway'] === $type) {
                return $competitor['id'];
            }
        }
        return null;
    }
}
