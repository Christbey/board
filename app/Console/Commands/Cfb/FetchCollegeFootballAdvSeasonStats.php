<?php

namespace App\Console\Commands\Cfb;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Traits\CollegeFootballAdvSeasonTrait;

class FetchCollegeFootballAdvSeasonStats extends Command
{
    use CollegeFootballAdvSeasonTrait;

    protected $signature = 'fetch:college-football-adv-season-stats';

    protected $description = 'Fetch and store college football advanced season stats from API';

    public function handle()
    {
        $url = 'https://api.collegefootballdata.com/stats/season/advanced?year=2023';
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'Authorization' => 'Bearer ' . env('COLLEGE_FOOTBALL_DATA_API_KEY')
        ])->get($url);

        if ($response->successful()) {
            $stats = $response->json();

            foreach ($stats as $stat) {
                // Process and store each stat
                $this->processStats($stat);
            }

            $this->info('College football advanced season stats fetched and stored successfully.');
        } else {
            $this->error('Failed to fetch the data.');
        }
    }
}
