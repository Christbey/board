<?php

namespace App\Console\Commands\Espn;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class EspnNflPlayerDepthChart extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'espn:nfl-player-depth-chart';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch NFL player depth chart from the ESPN API';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $url = 'https://sports.core.api.espn.com/v2/sports/football/leagues/nfl/seasons/2024/teams/24/depthcharts';

        // Send a GET request to the endpoint
        $response = Http::get($url);

        if ($response->successful()) {
            $data = $response->json();

            // Extract depth chart information
            $items = $data['items'];
            foreach ($items as $item) {
                $this->info("Depth Chart ID: {$item['id']}, Name: {$item['name']}");
                $positions = $item['positions'];
                foreach ($positions as $positionKey => $position) {
                    $this->info("Position: {$position['position']['displayName']}");
                    foreach ($position['athletes'] as $athlete) {
                        $athleteDetails = Http::get($athlete['athlete']['$ref'])->json();
                        $jersey = $athleteDetails['jersey'] ?? null;
                        $this->info("  - Name: {$athleteDetails['fullName']}, Jersey: {$jersey}, Position: {$athleteDetails['position']['name']}, Rank: {$athlete['rank']}");
                    }
                }
            }

            // Optionally save the response to a file
            // file_put_contents(storage_path('app/depthcharts.json'), json_encode($data, JSON_PRETTY_PRINT));

            return Command::SUCCESS;
        } else {
            $this->error('Failed to retrieve data: ' . $response->status());
            return Command::FAILURE;
        }
    }
}
