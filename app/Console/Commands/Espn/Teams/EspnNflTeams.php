<?php

namespace App\Console\Commands\Espn\Teams;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class EspnNflTeams extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'espn:nfl-teams';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch NFL teams from the ESPN API';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $url = 'https://site.api.espn.com/apis/site/v2/sports/football/nfl/teams';

        // Send a GET request to the endpoint
        $response = Http::get($url);

        if ($response->successful()) {
            $data = $response->json();

            // Extract team information
            $teams = $data['sports'][0]['leagues'][0]['teams'];
            $teamInfo = collect($teams)->map(function ($team) {
                return [
                    'id' => $team['team']['id'],
                    'uid' => $team['team']['uid'],
                    'slug' => $team['team']['slug'],
                    'abbreviation' => $team['team']['abbreviation'],
                    'displayName' => $team['team']['displayName'],
                    'name' => $team['team']['name'],
                    'nickname' => $team['team']['nickname'],
                    'location' => $team['team']['location'],
                    'color' => $team['team']['color'],
                    'alternateColor' => $team['team']['alternateColor'],
                ];
            });

            // Output the team information
            $this->info('NFL Teams:');
            $teamInfo->each(function ($team) {
                $this->info("ID: {$team['id']}, UID: {$team['uid']}, Slug: {$team['slug']}, Abbreviation: {$team['abbreviation']}, Display Name: {$team['displayName']}, Short Display Name: {$team['shortDisplayName']}, Name: {$team['name']}, Nickname: {$team['nickname']}, Location: {$team['location']}, Color: {$team['color']}, Alternate Color: {$team['alternateColor']}");
            });

            // Optionally save the response to a file
            // file_put_contents(storage_path('app/response.json'), json_encode($data, JSON_PRETTY_PRINT));

            return Command::SUCCESS;
        } else {
            $this->error('Failed to retrieve data: ' . $response->status());
            return Command::FAILURE;
        }
    }
}
