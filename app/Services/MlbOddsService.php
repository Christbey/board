<?php

namespace App\Services;

class MlbOddsService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.odds_api.key'); // Ensure you have the API key in your config/services.php
        $this->baseUrl = 'https://api.the-odds-api.com/v4'; // Base URL for the API
    }

    public function fetchOdds()
    {
        // Your logic to fetch odds from the API
        // This is just an example and should be modified according to your actual implementation
        $url = $this->baseUrl . '/sports/baseball_mlb/odds?apiKey=' . $this->apiKey . '&regions=us&markets=h2h,spreads,totals';
        $response = file_get_contents($url);

        if ($response === false) {
            throw new \Exception('Error fetching MLB odds');
        }

        return json_decode($response, true);
    }
}
