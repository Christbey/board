<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class NFLStatsService
{
    protected Client $client;
    protected string $baseUrl;
    protected mixed $apiKey;
    protected mixed $apiHost;

    public function __construct()
    {
        $this->client = new Client();
        $this->baseUrl = config('services.rapidapi.base_url');
        $this->apiKey = config('services.rapidapi.key');
        $this->apiHost = config('services.rapidapi.host');
    }

    public function getNFLTeams($schedules = false, $rosters = false, $topPerformers = false, $teamStats = false)
    {
        $params = [
            'schedules' => $schedules ? 'true' : 'false',
            'rosters' => $rosters ? 'true' : 'false',
            'topPerformers' => $topPerformers ? 'true' : 'false',
            'teamStats' => $teamStats ? 'true' : 'false'
        ];

        return $this->makeApiRequest('/getNFLTeams', $params);
    }

    public function getNFLBoxScore($gameID, $playByPlay = false)
    {
        $params = [
            'gameID' => $gameID,
            'playByPlay' => $playByPlay ? 'true' : 'false'
        ];

        return $this->makeApiRequest('/getNFLBoxScore', $params);
    }

    public function getNFLPlayerList()
    {
        return $this->makeApiRequest('/getNFLPlayerList');
    }

    public function getNFLGamesForPlayer($playerID, $fantasyPoints = true, $twoPointConversions = 2, $passYards = 0.04, $passTD = 4, $passInterceptions = -2, $pointsPerReception = 1, $carries = 0.2, $rushYards = 0.1, $rushTD = 6, $fumbles = -2, $receivingYards = 0.1, $receivingTD = 6, $targets = 0, $defTD = 6, $xpMade = 1, $xpMissed = -1, $fgMade = 3, $fgMissed = -3)
    {
        $params = [
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
        ];

        return $this->makeApiRequest('/getNFLGamesForPlayer', $params);
    }

    public function getNFLTeamRoster($teamID, $teamAbv)
    {
        $params = [
            'teamID' => $teamID,
            'teamAbv' => $teamAbv,
            'getStats' => 'true'
        ];

        return $this->makeApiRequest('/getNFLTeamRoster', $params);
    }

    public function getNFLNews($playerID = null, $topNews = true, $fantasyNews = false, $recentNews = false, $maxItems = 10)
    {
        $params = [
            'playerID' => $playerID,
            'topNews' => $topNews ? 'true' : 'false',
            'fantasyNews' => $fantasyNews ? 'true' : 'false',
            'recentNews' => $recentNews ? 'true' : 'false',
            'maxItems' => $maxItems
        ];

        return $this->makeApiRequest('/getNFLNews', $params);
    }

    private function makeApiRequest(string $endpoint, array $queryParams = [])
    {
        $url = $this->baseUrl . $endpoint;
        $params = [
            'query' => $queryParams,
            'headers' => [
                'x-rapidapi-host' => $this->apiHost,
                'x-rapidapi-key' => $this->apiKey,
            ],
        ];

        try {
            $response = $this->client->request('GET', $url, $params);
            $responseBody = $response->getBody()->getContents();
            Log::info('API Response: ' . $responseBody);
            return json_decode($responseBody, true);
        } catch (RequestException $e) {
            Log::error('API Request failed: ' . $e->getMessage());
            return null;
        }
    }
}
