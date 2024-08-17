<?php

namespace App\Console\Commands\Cfb\Stat;

use App\Traits\CollegeFootball\CollegeFootballAdvSeasonTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchCollegeFootballAdvSeasonStats extends Command
{
    use CollegeFootballAdvSeasonTrait;

    protected $signature = 'fetch:college-football-adv-season-stats {year?}';
    protected $description = 'Fetch and store college football advanced season stats from API';

    public function handle()
    {
        $year = $this->argument('year') ?? config('collegefootball.default_year');
        $url = $this->buildApiUrl($year);

        $response = $this->fetchStatsFromApi($url);

        if ($response->successful()) {
            $this->processAllStats($response->json());
            $this->info('College football advanced season stats fetched and stored successfully.');
        } else {
            $this->error('Failed to fetch the data.');
        }
    }

    private function buildApiUrl($year)
    {
        return config('collegefootball.api_base_url') . "/stats/season/advanced?year=$year";
    }

    private function fetchStatsFromApi($url)
    {
        return Http::withHeaders([
            'accept' => 'application/json',
            'Authorization' => 'Bearer ' . config('collegefootball.api_key'),
        ])->get($url);
    }

    private function processAllStats(array $stats)
    {
        foreach ($stats as $stat) {
            // Process and store each stat using the trait method
            $this->processStats($stat);
        }
    }
}
