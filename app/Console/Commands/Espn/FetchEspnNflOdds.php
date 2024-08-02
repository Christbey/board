<?php

namespace App\Console\Commands\Espn;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class FetchEspnNflOdds extends Command
{
    protected $signature = 'fetch:espn-nfl-odds';
    protected $description = 'Fetch NFL odds from ESPN API';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $url = 'https://sports.core.api.espn.com/v2/sports/football/leagues/nfl/teams/10/odds/1002/past-performances?limit=200';
        $response = Http::get($url);

        if ($response->successful()) {
            $data = $response->json();
            $oddsData = $data['items'];

            foreach ($oddsData as &$item) {
                if (isset($item['pastCompetition']['$ref'])) {
                    $competitionUrl = htmlspecialchars_decode($item['pastCompetition']['$ref'], ENT_QUOTES);
                    Log::info("Fetching past competition data from $competitionUrl");
                    try {
                        $competitionResponse = Http::timeout(60)->retry(3, 100)->get($competitionUrl);
                        if ($competitionResponse->successful()) {
                            $item['pastCompetitionData'] = $competitionResponse->json();

                            // Fetch teams data from pastCompetitionData
                            $competitors = $item['pastCompetitionData']['competitors'];
                            foreach ($competitors as &$competitor) {
                                if (isset($competitor['team']['$ref'])) {
                                    $teamUrl = htmlspecialchars_decode($competitor['team']['$ref'], ENT_QUOTES);
                                    try {
                                        $teamResponse = Http::timeout(60)->retry(3, 100)->get($teamUrl);
                                        if ($teamResponse->successful()) {
                                            $competitor['teamData'] = $teamResponse->json();
                                        } else {
                                            Log::error("Failed to fetch team data from $teamUrl");
                                        }
                                    } catch (Exception $e) {
                                        Log::error('Connection error: ' . $e->getMessage());
                                    }
                                }
                            }

                        } else {
                            Log::error("Failed to fetch past competition data from $competitionUrl");
                        }
                    } catch (Exception $e) {
                        Log::error('Connection error: ' . $e->getMessage());
                    }
                }
            }

            Storage::put('public/nfl_odds.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            $this->info('NFL odds fetched and stored successfully.');
        } else {
            $this->error('Failed to fetch NFL odds.');
        }
    }
}
