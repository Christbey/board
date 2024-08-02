<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class FetchEspnNflTeamProjection extends Command
{
    protected $signature = 'fetch:espn-nfl-team-projection';
    protected $description = 'Fetch NFL team projection from ESPN API';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $url = 'https://sports.core.api.espn.com/v2/sports/football/leagues/nfl/seasons/2023/teams/6/projection';
        $response = Http::get($url);

        if ($response->successful()) {
            $data = $response->json();
            Storage::put('public/nfl_team_projection.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            $this->info('NFL team projection fetched and stored successfully.');
        } else {
            $this->error('Failed to fetch NFL team projection.');
        }
    }
}
