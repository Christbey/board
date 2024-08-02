<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Models\NflEspnTeam;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class FetchNflTeams extends Command
{
    protected $signature = 'fetch:nfl-teams';
    protected $description = 'Fetch and store NFL teams data from ESPN API';

    protected $client;

    public function __construct()
    {
        parent::__construct();
        $this->client = new Client();
    }

    public function handle()
    {
        $urls = Config::get('espn.urls');

        foreach ($urls as $url) {
            $this->fetchAndStoreTeamData($url);
        }

        $this->info('NFL teams data fetched and stored successfully.');
    }

    private function fetchAndStoreTeamData($url)
    {
        $response = $this->client->request('GET', $url);
        $teamData = json_decode($response->getBody()->getContents(), true);

        // Extract team ID from URL
        preg_match('/teams\/(\d+)\?/', $url, $matches);
        $teamId = $matches[1] ?? null;

        if (!$teamId) {
            Log::error('Team ID not found in the URL', ['url' => $url]);
            throw new Exception('Team ID not found in the URL');
        }

        Log::info('Team Data:', $teamData);

        NflEspnTeam::updateOrCreate(
            ['team_id' => $teamId],
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
