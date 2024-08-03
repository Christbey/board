<?php

namespace App\Console\Commands\Espn\Teams;

use App\Models\NflEspnTeam;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchEspnNflTeams extends Command
{
    protected $signature = 'espn:fetch-nfl-teams';
    protected $description = 'Fetch NFL teams from the ESPN API and store them in the database';

    public function handle()
    {
        $url = 'https://site.api.espn.com/apis/site/v2/sports/football/nfl/teams';

        $response = Http::get($url);

        if ($response->successful()) {
            $teams = $response->json()['sports'][0]['leagues'][0]['teams'];

            foreach ($teams as $teamData) {
                $team = $teamData['team'];

                NflEspnTeam::updateOrCreate(
                    ['team_id' => $team['id']],
                    [
                        'uid' => $team['uid'],
                        'slug' => $team['slug'],
                        'abbreviation' => $team['abbreviation'],
                        'display_name' => $team['displayName'],
                        'short_display_name' => $team['shortDisplayName'],
                        'name' => $team['name'],
                        'nickname' => $team['nickname'],
                        'location' => $team['location'],
                        'color' => $team['color'],
                        'alternate_color' => $team['alternateColor'],
                        'is_active' => $team['isActive'],
                    ]
                );
            }

            $this->info('Teams data has been fetched and stored successfully.');
        } else {
            $this->error('Failed to fetch teams data.');
        }

        return 0;
    }
}
