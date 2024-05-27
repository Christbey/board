<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OddsService
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
        $params = [
            'apiKey' => $this->apiKey,
            'regions' => 'us',
            'markets' => $markets,
            'dateFormat' => 'iso',
            'oddsFormat' => 'american',
            'bookmakers' => 'draftkings',
        ];

        $response = Http::get("{$this->baseUrl}/sports/{$sport}/odds", $params);

        return $response->json();
    }
}
