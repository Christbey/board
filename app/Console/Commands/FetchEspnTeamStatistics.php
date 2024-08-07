<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\NflEspnTeamStat;
use App\Models\NflEspnTeam;

class FetchEspnTeamStatistics extends Command
{
    protected $signature = 'fetch:espn-team-statistics {year} {team_id?}';
    protected $description = 'Fetch ESPN team statistics and store them in the database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $year = $this->argument('year');
        $teamId = $this->argument('team_id');

        if ($teamId) {
            $this->fetchAndStoreStatistics($year, $teamId);
        } else {
            $teams = NflEspnTeam::all();
            foreach ($teams as $team) {
                $this->fetchAndStoreStatistics($year, $team->team_id);
            }
        }

        $this->info('Team statistics fetched and stored successfully.');
    }

    private function fetchAndStoreStatistics($year, $teamId)
    {
        $url = "https://sports.core.api.espn.com/v2/sports/football/leagues/nfl/seasons/{$year}/types/2/teams/{$teamId}/statistics";
        $response = Http::get($url);

        if ($response->successful()) {
            $data = $response->json();

            $statisticsData = [
                'season' => $this->getIdFromUrl($data['season']['$ref']),
                'team_id' => $this->getIdFromUrl($data['team']['$ref']),
                'splits' => []
            ];

            foreach ($data['splits']['categories'] as $category) {
                foreach ($category['stats'] as $stat) {
                    $statisticsData['splits'][] = [
                        'category' => $category['name'],
                        'stat_name' => $stat['name'],
                        'stat_value' => $stat['value'],
                        'stat_display_value' => $stat['displayValue'],
                        'stat_rank' => $stat['rank'] ?? null,
                        'stat_rank_display_value' => $stat['rankDisplayValue'] ?? null
                    ];
                }
            }

            // Store the data in the database using updateOrCreate to prevent duplication
            foreach ($statisticsData['splits'] as $split) {
                NflEspnTeamStat::updateOrCreate(
                    [
                        'season' => $statisticsData['season'],
                        'team_id' => $statisticsData['team_id'],
                        'stat_name' => $split['stat_name'],
                        'category' => $split['category'],
                    ],
                    [
                        'stat_value' => $split['stat_value'],
                        'stat_display_value' => $split['stat_display_value'],
                        'stat_rank' => $split['stat_rank'],
                        'stat_rank_display_value' => $split['stat_rank_display_value']
                    ]
                );
            }
        } else {
            $this->error("Failed to fetch data from ESPN API for team ID {$teamId}.");
        }
    }

    private function getIdFromUrl($url)
    {
        $parts = explode('/', parse_url($url, PHP_URL_PATH));
        return end($parts);
    }
}
