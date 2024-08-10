<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CollegeFootballConference;
use Illuminate\Support\Facades\Http;

class FetchCollegeFootballConferences extends Command
{
    protected $signature = 'fetch:college-football-conferences';
    protected $description = 'Fetch college football conferences from the API and save to database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'Authorization' => 'Bearer ' . env('COLLEGE_FOOTBALL_DATA_API_KEY'),
        ])->get('https://api.collegefootballdata.com/conferences');

        if ($response->successful()) {
            $conferences = $response->json();

            foreach ($conferences as $conference) {
                // Safely handle missing data
                $abbreviation = $conference['abbreviation'] ?? null;
                $name = $conference['name'] ?? null;
                $short_name = $conference['short_name'] ?? null;
                $classification = $conference['classification'] ?? null;

                // Skip the iteration if the 'name' is null
                if (is_null($name)) {
                    continue;
                }

                // Update or create the conference record
                CollegeFootballConference::updateOrCreate(
                    [
                        'abbreviation' => $abbreviation,
                    ],
                    [
                        'name' => $name,
                        'short_name' => $short_name,
                        'classification' => $classification,
                    ]
                );
            }

            $this->info('College football conferences fetched and saved successfully.');
        } else {
            $this->error('Failed to fetch data from the API.');
        }
    }
}
