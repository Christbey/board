<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class OddsService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.oddsapi.key');
        $this->baseUrl = config('services.oddsapi.base_url');
    }

    public function getOdds($sport, $markets)
    {
        try {
            $response = Http::get("{$this->baseUrl}/sports/{$sport}/odds", [
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
