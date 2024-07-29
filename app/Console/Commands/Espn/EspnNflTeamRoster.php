<?php

namespace App\Console\Commands\Espn;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class EspnNflTeamRoster extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'espn:nfl-team-roster';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch NFL team roster from the ESPN API';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $url = 'https://site.api.espn.com/apis/site/v2/sports/football/nfl/teams/4/roster';

        // Send a GET request to the endpoint
        $response = Http::get($url);

        if ($response->successful()) {
            $data = $response->json();

            // Extract roster information
            $athletes = $data['athletes'];
            foreach ($athletes as $athleteCategory) {
                foreach ($athleteCategory['items'] as $athlete) {
                    $id = $athlete['id'] ?? null;
                    $fullName = $athlete['fullName'] ?? null;
                    $jersey = $athlete['jersey'] ?? null;
                    $position = $athlete['position']['displayName'] ?? null;
                    $age = $athlete['age'] ?? null;
                    $height = $athlete['displayHeight'] ?? null;
                    $weight = $athlete['displayWeight'] ?? null;

                    $this->info("ID: {$id}, Name: {$fullName}, Jersey: {$jersey}, Position: {$position}, Age: {$age}, Height: {$height}, Weight: {$weight}");
                }
            }

            // Optionally save the response to a file
            // file_put_contents(storage_path('app/roster.json'), json_encode($data, JSON_PRETTY_PRINT));

            return Command::SUCCESS;
        } else {
            $this->error('Failed to retrieve data: ' . $response->status());
            return Command::FAILURE;
        }
    }
}
