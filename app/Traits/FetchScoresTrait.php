<?php
// app/Traits/FetchScoresTrait.php

namespace App\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

trait FetchScoresTrait
{
    public function fetchAndStoreScores($sport, $endpoint, $modelClass, $teamModelClass)
    {
        $baseUrl = env('ODDS_API_BASE_URL');
        $apiKey = env('ODDS_API_KEY');

        if (!$baseUrl) {
            Log::error('The base URL for the odds API is not set.');
            return;
        }

        $url = "{$baseUrl}/{$endpoint}";

        $response = Http::withOptions([
            'timeout' => 10, // Increase the timeout value as needed
            'connect_timeout' => 5,
        ])->retry(3, 100)->get($url, [
            'apiKey' => $apiKey,
            'daysFrom' => 3,
            'dateFormat' => 'iso'
        ]);

        // Log the response for debugging purposes
        Log::info(strtoupper($sport) . ' Scores API Response', ['response' => $response->json()]);

        if ($response->successful()) {
            $scores = $response->json();

            foreach ($scores as $score) {
                // Parse and convert the commence_time to CST and format it
                $commenceTime = Carbon::parse($score['commence_time'])->setTimezone('America/Chicago')->format('Y-m-d H:i:s');

                // Initialize scores
                $homeTeamScore = null;
                $awayTeamScore = null;

                // Check if the scores key exists and is an array
                if (isset($score['scores']) && is_array($score['scores'])) {
                    foreach ($score['scores'] as $teamScore) {
                        if ($teamScore['name'] === $score['home_team']) {
                            $homeTeamScore = $teamScore['score'];
                        } elseif ($teamScore['name'] === $score['away_team']) {
                            $awayTeamScore = $teamScore['score'];
                        }
                    }
                }

                $modelClass::updateOrCreate(
                    ['event_id' => $score['id']],
                    [
                        'sport_key' => $score['sport_key'],
                        'sport_title' => $score['sport_title'],
                        'commence_time' => $commenceTime,
                        'completed' => $score['completed'],
                        'home_team_id' => $teamModelClass::firstOrCreate(['name' => $score['home_team']])->id,
                        'away_team_id' => $teamModelClass::firstOrCreate(['name' => $score['away_team']])->id,
                        'home_team_score' => $homeTeamScore,
                        'away_team_score' => $awayTeamScore,
                        'last_update' => now(),
                    ]
                );
            }

            Log::info(strtoupper($sport) . ' scores fetched and stored in the database.');
        } else {
            Log::error('Failed to fetch ' . strtoupper($sport) . ' scores: ' . $response->body());
        }
    }
}
