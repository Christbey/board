<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NbaOddsService extends OddsService
{
    public function getOdds($sport, $markets)
    {
        try {
            $response = Http::get("https://api.the-odds-api.com/v4/sports/{$sport}/odds", [
                'apiKey' => $this->apiKey,
                'regions' => 'us',
                'markets' => $markets,
                'oddsFormat' => 'american',
                'dateFormat' => 'iso',
                'bookmakers' => 'draftkings',
            ]);

            $odds = $response->json();

            if ($response->failed() || isset($odds['error_code'])) {
                return $odds;
            }

            return $odds;
        } catch (\Exception $e) {
            Log::error('Error fetching odds: ' . $e->getMessage());
            throw $e;
        }
    }
}
