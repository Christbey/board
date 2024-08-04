<?php

namespace App\Console\Commands\Espn;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\EspnNflPastH2h;
use Carbon\Carbon;

class FetchEspnEventPastH2h extends Command
{
    protected $signature = 'fetch:espn-past-event-h2h {team_id}';
    protected $description = 'Fetch and store ESPN past head-to-head event data for a given team';

    public function handle()
    {
        $teamId = $this->argument('team_id');
        $url = "https://sports.core.api.espn.com/v2/sports/football/leagues/nfl/teams/{$teamId}/odds/1002/past-performances?limit=1000";

        $response = Http::get($url);

        if ($response->successful()) {
            $data = $response->json();

            foreach ($data['items'] as $item) {
                // Extract event_id from the pastCompetition URL
                $eventUrl = $item['pastCompetition']['$ref'];
                $eventId = $this->extractEventIdFromUrl($eventUrl);

                // Fetch competitors to determine home and away team IDs
                $competitorsUrl = "http://sports.core.api.espn.com/v2/sports/football/leagues/nfl/events/{$eventId}/competitions/{$eventId}?lang=en&region=us";
                $competitorsResponse = Http::get($competitorsUrl);

                if ($competitorsResponse->successful()) {
                    $competitorsData = $competitorsResponse->json();
                    $homeTeamId = null;
                    $awayTeamId = null;

                    foreach ($competitorsData['competitors'] as $competitor) {
                        if (isset($competitor['team']['$ref'])) {
                            $teamUrl = $competitor['team']['$ref'];
                            $teamResponse = Http::get($teamUrl);

                            if ($teamResponse->successful()) {
                                $teamData = $teamResponse->json();
                                if ($competitor['homeAway'] == 'home') {
                                    $homeTeamId = $teamData['id'];
                                } else {
                                    $awayTeamId = $teamData['id'];
                                }
                            } else {
                                Log::warning("Failed to fetch team data from URL: {$teamUrl}");
                            }
                        } else {
                            Log::warning('Missing team reference in competitor data', $competitor);
                        }
                    }

                    if ($homeTeamId && $awayTeamId) {
                        // Determine the correct winner values based on the team_id from the URL
                        $isHomeTeam = ($homeTeamId == $teamId);
                        $isAwayTeam = ($awayTeamId == $teamId);

                        EspnNflPastH2h::updateOrCreate(
                            [
                                'event_id' => $eventId,
                                'home_team_id' => $homeTeamId,
                                'away_team_id' => $awayTeamId
                            ],
                            [
                                'spread' => $item['spread'],
                                'over_odds' => $item['overOdds'],
                                'under_odds' => $item['underOdds'],
                                'away_team_money_line_odds' => $item['moneyLineOdds'],
                                'away_team_spread_odds' => $item['spreadOdds'],
                                'away_team_spread_winner' => $isAwayTeam ? $item['spreadWinner'] : !$item['spreadWinner'],
                                'away_team_money_line_winner' => $isAwayTeam ? $item['moneylineWinner'] : !$item['moneylineWinner'],
                                'home_team_money_line_odds' => $item['moneyLineOdds'], // Adjust if needed
                                'home_team_spread_odds' => $item['spreadOdds'],       // Adjust if needed
                                'home_team_spread_winner' => $isHomeTeam ? $item['spreadWinner'] : !$item['spreadWinner'],   // Adjust if needed
                                'home_team_money_line_winner' => $isHomeTeam ? $item['moneylineWinner'] : !$item['moneylineWinner'], // Adjust if needed
                                'line_date' => Carbon::createFromFormat('Y-m-d\TH:i\Z', $item['lineDate'])->toDateTimeString(),
                                'total_line' => $item['totalLine'],
                                'total_result' => $item['totalResult'],
                                'moneyline_winner' => $item['moneylineWinner'],
                                'spread_winner' => $item['spreadWinner'],
                            ]
                        );
                    } else {
                        Log::warning("Missing home or away team ID for event ID {$eventId}");
                    }
                } else {
                    Log::error("Failed to fetch competitors data for event ID {$eventId}");
                }
            }

            $this->info('Past performances data fetched and stored successfully.');
        } else {
            $this->error('Failed to fetch past performances data from the API.');
        }
    }

    private function extractEventIdFromUrl($url)
    {
        $segments = explode('/', parse_url($url, PHP_URL_PATH));
        return end($segments); // Extracts the last segment, which is the event_id
    }
}
