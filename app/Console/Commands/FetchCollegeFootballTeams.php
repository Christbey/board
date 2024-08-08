<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CollegeFootballTeam;
use Illuminate\Support\Facades\Http;

class FetchCollegeFootballTeams extends Command
{
    protected $signature = 'fetch:college-football-teams';
    protected $description = 'Fetch college football teams from the API and save to database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'Authorization' => 'Bearer 4b/N6meGdvO3k52FMU375HldXVcg+iNk6o/SMYATiNL3LUkg0LNRcvUKg97pbGrT',
        ])->get('https://api.collegefootballdata.com/teams');

        if ($response->successful()) {
            $teams = $response->json();

            foreach ($teams as $team) {
                $location = $team['location'];

                CollegeFootballTeam::updateOrCreate(
                    ['id' => $team['id']],
                    [
                        'school' => $team['school'],
                        'mascot' => $team['mascot'],
                        'abbreviation' => $team['abbreviation'],
                        'alt_name1' => $team['alt_name1'],
                        'alt_name2' => $team['alt_name2'],
                        'alt_name3' => $team['alt_name3'],
                        'conference' => $team['conference'],
                        'classification' => $team['classification'],
                        'color' => $team['color'],
                        'alt_color' => $team['alt_color'],
                        'logos' => json_encode($team['logos']),
                        'twitter' => $team['twitter'],
                        'venue_id' => $location['venue_id'] ?? null,
                        'venue_name' => $location['name'] ?? null,
                        'city' => $location['city'] ?? null,
                        'state' => $location['state'] ?? null,
                        'zip' => $location['zip'] ?? null,
                        'country_code' => $location['country_code'] ?? null,
                        'timezone' => $location['timezone'] ?? null,
                        'latitude' => $location['latitude'] ?? null,
                        'longitude' => $location['longitude'] ?? null,
                        'elevation' => $location['elevation'] ?? null,
                        'capacity' => $location['capacity'] ?? null,
                        'year_constructed' => $location['year_constructed'] ?? null,
                        'grass' => $location['grass'] ?? null,
                        'dome' => $location['dome'] ?? null,
                    ]
                );
            }

            $this->info('College football teams fetched and saved successfully.');
        } else {
            $this->error('Failed to fetch data from the API.');
        }
    }
}
