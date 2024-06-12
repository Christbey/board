<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use InvalidArgumentException;
use Exception;

class GetNflQbr extends Command
{
    protected $signature = 'nfl:get-qbr {season=2020} {week?} {season_type=Regular}';
    protected $description = 'Get NFL QBR';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $season = $this->argument('season');
        $week = $this->argument('week');
        $season_type = $this->argument('season_type');

        try {
            $result = $this->getNflQbr($season, $week, $season_type);
            $filteredResult = $this->filterJson($result);
            $this->info('QBR data fetched and filtered successfully:');
            $this->exportJson($filteredResult, "nfl_qbr_{$season}_{$week}_{$season_type}.json");
        } catch (Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }

    private function getNflQbr($season, $week, $season_type)
    {
        $current_year = date('Y');

        if (!in_array($season_type, ['Regular', 'Playoffs'])) {
            throw new InvalidArgumentException("Please choose season_type of 'Regular' or 'Playoffs'");
        }

        if ($season < 2006 || $season > $current_year) {
            throw new InvalidArgumentException("Please choose season between 2006 and $current_year");
        }

        if ($season_type == 'Regular' && !is_null($week) && ($week < 1 || $week > 18)) {
            throw new InvalidArgumentException('Please choose regular season week between 1 and 18');
        }

        if ($season_type == 'Playoffs' && !is_null($week) && ($week < 1 || $week > 4)) {
            throw new InvalidArgumentException('Please choose Playoff week between 1 and 4');
        }

        if ($season_type == 'Playoffs' && $week == 4 && $season == 2017) {
            $this->warn('ESPN has some missing Playoff data for 2017');
        }

        $week_current = ($season_type == 'Playoffs' && $week == 4 && $season >= 2009) ? 5 : $week;

        if (is_null($week)) {
            $this->info("Scraping QBR totals for $season!");
        } else {
            $this->info("Scraping weekly QBR for week $week of $season!");
        }

        $url_start = 'https://site.web.api.espn.com/apis/fitt/v3/sports/football/nfl/qbr';
        $query_type = [
            'qbrType' => is_null($week) ? 'seasons' : 'weeks',
            'seasontype' => ($season_type == 'Regular') ? 2 : 3,
            'isqualified' => 'true',
            'season' => $season
        ];

        if (!is_null($week)) {
            $query_type['week'] = $week_current;
        }

        $client = new Client();
        try {
            $response = $client->request('GET', $url_start, ['query' => $query_type]);
        } catch (RequestException $e) {
            throw new Exception('Failed to fetch data from ESPN API');
        }

        return json_decode($response->getBody()->getContents(), true);
    }

    private function filterJson($data)
    {
        $filteredData = [];

        if (isset($data['athletes'])) {
            foreach ($data['athletes'] as $athlete) {
                $filteredAthlete = [
                    'player_id' => $athlete['athlete']['id'] ?? 'N/A',
                    'player_name' => $athlete['athlete']['displayName'] ?? 'N/A',
                    'game_id' => $athlete['game']['id'] ?? 'N/A',
                    'game_date' => $athlete['game']['date'] ?? 'N/A',
                    'qbr' => $athlete['categories'][0]['totals'][0] ?? 'N/A', // Assuming QBR is the first total in categories
                ];
                $filteredData[] = $filteredAthlete;
            }
        }

        return $filteredData;
    }

    private function exportJson($data, $filename)
    {
        $filepath = storage_path('app/' . $filename);
        file_put_contents($filepath, json_encode($data, JSON_PRETTY_PRINT));
        $this->info("Data exported to {$filepath}");
    }
}
