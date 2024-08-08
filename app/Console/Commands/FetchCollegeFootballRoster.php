<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CollegeFootballTeam;
use App\Models\CollegeFootballRoster;
use Illuminate\Support\Facades\Http;

class FetchCollegeFootballRoster extends Command
{
    protected $signature = 'fetch:college-football-roster {year=2024}';
    protected $description = 'Fetch college football roster from the API and save to database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $year = $this->argument('year');
        $teams = CollegeFootballTeam::all();

        foreach ($teams as $team) {
            $teamName = urlencode($team->school);
            $response = Http::withHeaders([
                'accept' => 'application/json',
                'Authorization' => 'Bearer 4b/N6meGdvO3k52FMU375HldXVcg+iNk6o/SMYATiNL3LUkg0LNRcvUKg97pbGrT',
            ])->get("https://api.collegefootballdata.com/roster?team={$teamName}&year={$year}");

            if ($response->successful()) {
                $players = $response->json();

                foreach ($players as $player) {
                    CollegeFootballRoster::updateOrCreate(
                        ['player_id' => $player['id']],
                        [
                            'player_id' => $player['id'],
                            'first_name' => $player['first_name'],
                            'last_name' => $player['last_name'],
                            'team' => $player['team'],
                            'weight' => $player['weight'],
                            'height' => $player['height'],
                            'jersey' => $player['jersey'],
                            'year' => $player['year'],
                            'position' => $player['position'],
                            'home_city' => $player['home_city'],
                            'home_state' => $player['home_state'],
                            'home_country' => $player['home_country'],
                            'home_latitude' => $player['home_latitude'],
                            'home_longitude' => $player['home_longitude'],
                            'home_county_fips' => $player['home_county_fips'],
                            'recruit_ids' => $player['recruit_ids'],
                        ]
                    );
                }

                $this->info("Roster for team {$team->school} fetched and saved successfully.");
            } else {
                $this->error("Failed to fetch data for team {$team->school} from the API.");
            }
        }
    }
}
