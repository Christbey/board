<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CollegeFootballTalent;
use Illuminate\Support\Facades\Http;

class FetchCollegeFootballTalent extends Command
{
    protected $signature = 'fetch:college-football-talent {year=2023}';
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
                CollegeFootballTalent::updateOrCreate(
                    [
                        'year' => $talent['year'],
                        'school' => $talent['school']
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
