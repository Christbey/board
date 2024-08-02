<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;
use App\Models\NflEspnTeam;
use Log;

class ESPNService
{
    protected $client;
    protected $baseUri;
    protected $alternateBaseUris;
    protected $endpoints;

    public function __construct()
    {
        $this->baseUri = Config::get('espn.base_uri');
        $this->alternateBaseUris = Config::get('espn.alternate_base_uris');
        $this->endpoints = Config::get('espn.endpoints');
        $this->client = new Client();
    }

    public function getData($endpoint, $params = [], $base = 'default')
    {
        if (!isset($this->endpoints[$endpoint]['all'])) {
            throw new Exception('Endpoint configuration for ' . $endpoint . ' not found');
        }

        $baseUri = $base === 'default' ? $this->baseUri : $this->alternateBaseUris[$base];
        $endpointUrl = $this->endpoints[$endpoint]['all']; // Adjust endpoint based on configuration
        $response = $this->client->request('GET', $baseUri . $endpointUrl, ['query' => $params]);
        $data = json_decode($response->getBody()->getContents(), true);

        return $this->processRefs($data);
    }

    protected function processRefs($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $data[$key] = $this->processRefs($value);
                } elseif ($key === '$ref') {
                    $refData = $this->fetchRefData($value);
                    $data[$key] = $refData;
                }
            }
        }
        return $data;
    }

    protected function fetchRefData($ref)
    {
        $ref = str_replace('http://', 'https://', $ref); // Ensure using https
        $response = $this->client->request('GET', $ref);
        return json_decode($response->getBody()->getContents(), true);
    }

    public function saveFranchisesData($params = [], $base = 'core')
    {
        $endpoint = 'franchises';
        $data = $this->getData($endpoint, $params, $base);

        if (!isset($data['items'])) {
            Log::error('Items key not found in the response', $data);
            throw new Exception('Items key not found in the response');
        }

        foreach ($data['items'] as $item) {
            if (!isset($item['$ref'])) {
                Log::error('Reference key not found in the item', $item);
                throw new Exception('Reference key not found in the item');
            }

            $ref = $item['$ref'];

            if (is_array($ref)) {
                Log::error('Reference is an array', $ref);
                continue; // Skip items with incorrect references
            }

            $teamData = $this->fetchRefData($ref);

            // Log each team data structure for inspection
            Log::info('Team Data:', $teamData);

            if (!isset($teamData['id'])) {
                Log::error('Team ID not found in the data', $teamData);
                throw new Exception('Team ID not found in the data');
            }

            NflEspnTeam::updateOrCreate(
                ['team_id' => $teamData['id']],
                [
                    'uid' => $teamData['uid'] ?? null,
                    'slug' => $teamData['slug'] ?? null,
                    'abbreviation' => $teamData['abbreviation'] ?? null,
                    'display_name' => $teamData['displayName'] ?? null,
                    'short_display_name' => $teamData['shortDisplayName'] ?? null,
                    'name' => $teamData['name'] ?? null,
                    'nickname' => $teamData['nickname'] ?? null,
                    'location' => $teamData['location'] ?? null,
                    'color' => $teamData['color'] ?? null,
                    'alternate_color' => $teamData['alternateColor'] ?? null,
                    'is_active' => $teamData['isActive'] ?? null,
                ]
            );
        }
    }
}
