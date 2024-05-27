<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OddsApiService
{
    protected $apiKey;
    protected $baseUrl;
    protected $allowedSports = [
        'americanfootball_ncaaf',
        'americanfootball_nfl',
        'americanfootball_nfl_super_bowl_winner',
        'baseball_mlb',
        'basketball_nba'
    ];

    public function __construct()
    {
        $this->apiKey = config('services.oddsapi.key');
        $this->baseUrl = config('services.oddsapi.base_url');
    }

    public function getSports()
    {
        $response = Http::get("{$this->baseUrl}/sports", [
            'apiKey' => $this->apiKey,
        ]);

        $sports = $response->json();

        // Filter the sports to only include the allowed sports
        $filteredSports = array_filter($sports, function ($sport) {
            return in_array($sport['key'], $this->allowedSports);
        });

        return array_values($filteredSports); // Reset array keys
    }

    public function getOdds($sport, $markets)
    {
        $bookmakers = 'draftkings';
        $params = [
            'apiKey' => $this->apiKey,
            'regions' => 'us',
            'markets' => $markets,
            'dateFormat' => 'iso',
            'oddsFormat' => 'american',
            'bookmakers' => $bookmakers,
        ];

        $response = Http::get("{$this->baseUrl}/sports/{$sport}/odds", $params);

        return $response->json();
    }
}
