<?php

namespace App\Console\Commands\Espn\Teams;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class EspnNflSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'espn:nfl-schedule {team_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch NFL team schedule from the ESPN API';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $team_id = $this->argument('team_id');
        $url = "https://site.api.espn.com/apis/site/v2/sports/football/nfl/teams/{$team_id}/schedule";

        // Send a GET request to the endpoint
        $response = Http::get($url);

        if ($response->successful()) {
            $data = $response->json();

            // Return JSON data
            $this->output->writeln(json_encode($data));

            return Command::SUCCESS;
        } else {
            $this->error('Failed to retrieve data: ' . $response->status());
            return Command::FAILURE;
        }
    }
}
