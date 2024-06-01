<?php
// app/Services/NFLScoresService.php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NflScoresService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('ODDS_API_KEY'); // Ensure you have this in your .env file
    }

    public function fetchScores()
    {
        try {
            $response = Http::get('https://api.the-odds-api.com/v4/sports/americanfootball_nfl/scores', [
                'apiKey' => $this->apiKey,
            ]);

            if ($response->successful()) {
                $scores = $response->json();

                // Ensure $scores is an array
                if (!is_array($scores)) {
                    $scores = [];
                }

                return $scores;
            } else {
                Log::error('Error fetching NFL scores', ['response' => $response->body()]);
                return [];
            }
        } catch (\Exception $e) {
            Log::error('Exception fetching NFL scores', ['exception' => $e->getMessage()]);
            return [];
        }
    }
}
