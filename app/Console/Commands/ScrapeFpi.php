<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class ScrapeFpi extends Command
{
    protected $signature = 'scrape:fpi {season=2020} {stat=FPI}';
    protected $description = 'Scrape ESPN FPI ratings for a specific season';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $season = $this->argument('season');
        $stat = $this->argument('stat');

        try {
            $this->info("Scraping $stat for $season!");
            $data = $this->scrapeFpi($season, $stat);
            $this->saveDataAsJson($data, $season, $stat);
            $this->info('Data saved successfully.');
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }

    private function scrapeFpi($season, $stat)
    {
        $currentYear = (int) date('Y');

        if ($season < 2015 || $season > $currentYear) {
            throw new \InvalidArgumentException("Season must be between 2015 and $currentYear");
        }

        if (!in_array($stat, ['FPI', 'EFF', 'PROJ'])) {
            throw new \InvalidArgumentException("Stat must be one of 'FPI', 'EFF' or 'PROJ'");
        }

        $client = new Client();
        $url = $this->getUrl($season, $stat);
        $response = $client->request('GET', $url);
        $html = $response->getBody()->getContents();
        $crawler = new Crawler($html);

        return $this->extractData($crawler, $season, $stat);
    }

    private function getUrl($season, $stat)
    {
        $base_url = 'https://www.espn.com/nfl/fpi/_/season/';

        switch ($stat) {
            case 'FPI':
                return $base_url . $season;
            case 'PROJ':
                return $base_url . 'view/projections/season/' . $season;
            case 'EFF':
                return $base_url . 'view/efficiencies/season/' . $season;
        }
    }

    private function extractData(Crawler $crawler, $season, $stat)
    {
        $data = [];
        $trendData = [];

        if ($stat === 'FPI') {
            $trendData = $crawler->filter('td:nth-child(4) > div')->each(function (Crawler $node) {
                return $node->attr('class');
            });
        }

        $crawler->filter('table')->first()->filter('tr')->each(function (Crawler $row, $i) use (&$data, $season, $stat, $trendData) {
            if ($i === 0) return; // Skip header row

            $rowData = $row->filter('td')->each(function (Crawler $cell) {
                return $cell->text();
            });

            $this->info('Extracted row data: ' . print_r($rowData, true));

            if ($stat === 'FPI') {
                $fpiNames = [
                    'team', 'w_l', 'fpi', 'rk', 'trend', 'off',
                    'def', 'st', 'sos', 'rem_sos', 'avgwp'
                ];

                if (count($rowData) == count($fpiNames)) {
                    $rowData = array_combine($fpiNames, $rowData);

                    $rowData['fpi'] = (float) $rowData['fpi'];
                    $rowData['off'] = (float) $rowData['off'];
                    $rowData['def'] = (float) $rowData['def'];
                    $rowData['st'] = (float) $rowData['st'];
                    $rowData['rk'] = (int) $rowData['rk'];
                    $rowData['trend'] = $trendData[$rowData['rk'] - 1] == 'trend negative' ? -(int) $rowData['trend'] : (int) $rowData['trend'];
                    $rowData['sos'] = (int) $rowData['sos'];
                    $rowData['rem_sos'] = (int) $rowData['rem_sos'];
                    $rowData['avgwp'] = (int) $rowData['avgwp'];
                    $rowData['season'] = $season;

                    $data[] = $rowData;
                }
            } else {
                if (count($rowData) > 1) {
                    $rowData['season'] = $season;
                    $data[] = $rowData;
                }
            }
        });

        return $data;
    }

    private function saveDataAsJson($data, $season, $stat)
    {
        if (!empty($data)) {
            $filename = storage_path("app/fpi_{$season}_{$stat}.json");
            file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT));
            $this->info('Data saved to ' . $filename);
        } else {
            $this->info('No data found to save.');
        }
    }
}
