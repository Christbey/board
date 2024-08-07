<?php

namespace App\Console\Commands\Espn\Season;

use App\Models\NflEspnTeam;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class NflSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'espn:nfl-schedule {team_id?}';

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
        $teamId = $this->argument('team_id');

        if ($teamId) {
            // Fetch and process schedule for the specific team ID
            $this->fetchAndProcessSchedule($teamId);
        } else {
            // Fetch and process schedule for all teams in the nfl_espn_teams table
            $teams = NflEspnTeam::all('team_id');
            foreach ($teams as $team) {
                $this->fetchAndProcessSchedule($team->team_id);
            }
        }

        return Command::SUCCESS;
    }

    private function fetchAndProcessSchedule($teamId)
    {
        $url = "https://site.api.espn.com/apis/site/v2/sports/football/nfl/teams/{$teamId}/schedule";

        // Send a GET request to the endpoint
        $response = Http::get($url);

        if ($response->successful()) {
            $data = $response->json();

            // Output JSON data to the terminal
            $this->output->writeln(json_encode($data));

            $this->info("NFL schedule for team {$teamId} fetched successfully.");
        } else {
            $this->error('Failed to retrieve data for team ' . $teamId . ': ' . $response->status());
        }
    }
}
