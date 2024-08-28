<?php

namespace App\Console\Commands\Cfb\Stat;

use App\Models\CollegeFootballTalent;
use App\Models\CollegeFootballTeam;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchCollegeFootballTalent extends Command
{
    protected $signature = 'fetch:college-football-talent {year=2024}';
    protected $description = 'Fetch college football talent data from the API and save to database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $year = $this->argument('year');
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'Authorization' => 'Bearer 4b/N6meGdvO3k52FMU375HldXVcg+iNk6o/SMYATiNL3LUkg0LNRcvUKg97pbGrT',
        ])->get("https://api.collegefootballdata.com/talent?year={$year}");

        if ($response->successful()) {
            $talents = $response->json();

            foreach ($talents as $talent) {
                // Find the corresponding college football team by school name, or create it if it doesn't exist
                $team = CollegeFootballTeam::firstOrCreate(
                    ['school' => $talent['school']],
                    [
                        'mascot' => 'Unknown', // You can set this to a default value or leave it null
                        'abbreviation' => 'UNK', // Default or null
                        'conference' => 'Unknown', // Default or null
                        'classification' => 'fbs', // Assuming all fetched teams are FBS
                    ]
                );

                // Now that the team exists, update or create the talent record
                CollegeFootballTalent::updateOrCreate(
                    [
                        'team_id' => $team->id, // Use team_id as the primary key
                    ],
                    [
                        'year' => $talent['year'],
                        'school' => $talent['school'],
                        'talent' => $talent['talent'],
                    ]
                );
            }

            $this->info("College football talent data for year {$year} fetched and saved successfully.");
        } else {
            $this->error('Failed to fetch data from the API.');
        }
    }
}
