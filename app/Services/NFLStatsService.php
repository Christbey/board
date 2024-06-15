<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class NFLStatsService
{
    protected $client;
    protected $baseUrl;
    protected $apiKey;
    protected $apiHost;

    public function __construct()
    {
        $this->client = new Client();
        $this->baseUrl = 'https://tank01-nfl-live-in-game-real-time-statistics-nfl.p.rapidapi.com';
        $this->apiKey = env('RAPIDAPI_KEY');
        $this->apiHost = env('RAPIDAPI_HOST');
    }

    public function getNFLTeams($schedules = false, $rosters = false, $topPerformers = false, $teamStats = false)
    {
        $url = $this->baseUrl . '/getNFLTeams';
        $params = [
            'query' => [
                'schedules' => $schedules ? 'true' : 'false',
                'rosters' => $rosters ? 'true' : 'false',
                'topPerformers' => $topPerformers ? 'true' : 'false',
                'teamStats' => $teamStats ? 'true' : 'false'
            ],
            'headers' => [
                'x-rapidapi-host' => $this->apiHost,
                'x-rapidapi-key' => $this->apiKey,
            ],
        ];

        try {
            $response = $this->client->request('GET', $url, $params);
            $responseBody = $response->getBody()->getContents();
            \Log::info('API Response: ' . $responseBody);
            return json_decode($responseBody, true);
        } catch (RequestException $e) {
            \Log::error('API Request failed: ' . $e->getMessage());
            return null;
        }
    }

    public function getNFLBoxScore($gameID, $playByPlay = false)
    {
        $url = $this->baseUrl . '/getNFLBoxScore';
        $params = [
            'query' => [
                'gameID' => $gameID,
                'playByPlay' => $playByPlay ? 'true' : 'false'
            ],
            'headers' => [
                'x-rapidapi-host' => $this->apiHost,
                'x-rapidapi-key' => $this->apiKey,
            ],
        ];

        try {
            $response = $this->client->request('GET', $url, $params);
            $responseBody = $response->getBody()->getContents();
            \Log::info('API Response: ' . $responseBody);
            return json_decode($responseBody, true);
        } catch (RequestException $e) {
            \Log::error('API Request failed: ' . $e->getMessage());
            return null;
        }
    }

    public function getNFLPlayerList()
    {
        $url = $this->baseUrl . '/getNFLPlayerList';
        $params = [
            'headers' => [
                'x-rapidapi-host' => $this->apiHost,
                'x-rapidapi-key' => $this->apiKey,
            ],
        ];

        $response = $this->client->request('GET', $url, $params);
        $responseBody = $response->getBody()->getContents();
        \Log::info('API Response: ' . $responseBody);  // Log the response body
        return json_decode($responseBody, true);
    }

    public function getNFLGamesForPlayer($playerID, $fantasyPoints = true, $twoPointConversions = 2, $passYards = 0.04, $passTD = 4, $passInterceptions = -2, $pointsPerReception = 1, $carries = 0.2, $rushYards = 0.1, $rushTD = 6, $fumbles = -2, $receivingYards = 0.1, $receivingTD = 6, $targets = 0, $defTD = 6, $xpMade = 1, $xpMissed = -1, $fgMade = 3, $fgMissed = -3)
    {
        $url = $this->baseUrl . '/getNFLGamesForPlayer';
        $params = [
            'query' => [
                'playerID' => $playerID,
                'fantasyPoints' => $fantasyPoints ? 'true' : 'false',
                'twoPointConversions' => $twoPointConversions,
                'passYards' => $passYards,
                'passTD' => $passTD,
                'passInterceptions' => $passInterceptions,
                'pointsPerReception' => $pointsPerReception,
                'carries' => $carries,
                'rushYards' => $rushYards,
                'rushTD' => $rushTD,
                'fumbles' => $fumbles,
                'receivingYards' => $receivingYards,
                'receivingTD' => $receivingTD,
                'targets' => $targets,
                'defTD' => $defTD,
                'xpMade' => $xpMade,
                'xpMissed' => $xpMissed,
                'fgMade' => $fgMade,
                'fgMissed' => $fgMissed
            ],
            'headers' => [
                'x-rapidapi-host' => $this->apiHost,
                'x-rapidapi-key' => $this->apiKey,
            ],
        ];

        try {
            $response = $this->client->request('GET', $url, $params);
            $responseBody = $response->getBody()->getContents();
            \Log::info('API Response: ' . $responseBody);  // Log the response body
            return json_decode($responseBody, true);
        } catch (RequestException $e) {
            \Log::error('API Request failed: ' . $e->getMessage());
            return null;
        }
    }

    public function getNFLTeamRoster($teamID, $teamAbv)
    {
        $url = $this->baseUrl . '/getNFLTeamRoster';
        $params = [
            'query' => [
                'teamID' => $teamID,
                'teamAbv' => $teamAbv,
                'getStats' => 'true'
            ],
            'headers' => [
                'x-rapidapi-host' => $this->apiHost,
                'x-rapidapi-key' => $this->apiKey,
            ],
        ];

        try {
            $response = $this->client->request('GET', $url, $params);
            $responseBody = $response->getBody()->getContents();
            \Log::info('API Response: ' . $responseBody);
            return json_decode($responseBody, true);
        } catch (RequestException $e) {
            \Log::error('API Request failed: ' . $e->getMessage());
            return null;
        }
    }

    // In App\Services\NFLStatsService.php

    // In App\Services\NFLStatsService.php

    public function getNFLNews($playerID = null, $topNews = true, $fantasyNews = false, $recentNews = false, $maxItems = 10)
    {
        $url = $this->baseUrl . '/getNFLNews';
        $params = [
            'query' => [
                'playerID' => $playerID,
                'topNews' => $topNews ? 'true' : 'false',
                'fantasyNews' => $fantasyNews ? 'true' : 'false',
                'recentNews' => $recentNews ? 'true' : 'false',
                'maxItems' => $maxItems
            ],
            'headers' => [
                'x-rapidapi-host' => $this->apiHost,
                'x-rapidapi-key' => $this->apiKey,
            ],
        ];

        try {
            $response = $this->client->request('GET', $url, $params);
            $responseBody = $response->getBody()->getContents();
            \Log::info('API Response: ' . $responseBody);  // Log the response body
            return json_decode($responseBody, true);
        } catch (RequestException $e) {
            \Log::error('API Request failed: ' . $e->getMessage());
            return null;
        }
    }


}
