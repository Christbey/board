<?php

namespace App\Console\Commands\Espn\Teams;

use App\Models\NflEspnTeam;
use App\Models\NflEspnTeamProjection;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchEspnNflTeamProjection extends Command
{
    protected $signature = 'fetch:espn-nfl-team-projection {team_id?}';
    protected $description = 'Fetch NFL team projection from ESPN API';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $teamId = $this->argument('team_id');
        $teams = $teamId ? NflEspnTeam::where('team_id', $teamId)->get() : NflEspnTeam::all();

        foreach ($teams as $team) {
            $url = "https://sports.core.api.espn.com/v2/sports/football/leagues/nfl/seasons/2024/teams/{$team->team_id}/projection";
            $response = Http::get($url);

            if ($response->successful()) {
                $data = $response->json();

                NflEspnTeamProjection::updateOrCreate(
                    ['team_id' => $team->team_id],
                    [
                        'chance_to_win_division' => $data['chanceToWinDivision'],
                        'projected_wins' => $data['projectedWins'],
                        'projected_losses' => $data['projectedLosses'],
                    ]
                );

                $this->info("NFL team projection for team {$team->display_name} fetched and stored successfully.");
            } else {
                $this->error("Failed to fetch NFL team projection for team {$team->display_name}.");
            }
        }
    }
}
