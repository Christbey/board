<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NbaScoreService
{
    public function getScores()
    {
        $response = Http::get('https://api.the-odds-api.com/v4/sports/basketball_nba/scores', [
            'apiKey' => '9f1d9176fa7c6c47ea169f2ff007c8fa',
            'daysFrom' => 3,
            'dateFormat' => 'iso'
        ]);

        if ($response->successful()) {
            $scores = $response->json();
            Log::info('NBA Scores API Response: ', $scores);
            return $scores;
        }

        Log::error('Failed to fetch NBA scores', ['status' => $response->status()]);
        return [];
    }
}
