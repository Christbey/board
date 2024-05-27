<?php

namespace App\Listeners;

use App\Events\MlbOddsFetched;
use App\Models\MlbOdds;
use App\Models\MlbOddsHistory;
use App\Models\MlbTeam;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class StoreMlbOdds
{
    public function handle(MlbOddsFetched $event)
    {
        Log::info('StoreMlbOdds listener triggered.');

        foreach ($event->odds as $eventData) {
            $homeTeam = MlbTeam::firstOrCreate(['name' => $eventData['home_team']]);
            $awayTeam = MlbTeam::firstOrCreate(['name' => $eventData['away_team']]);

            if (!$homeTeam || !$awayTeam) {
                Log::warning("Teams not found: Home - {$eventData['home_team']}, Away - {$eventData['away_team']}");
                continue; // Skip if teams are not found
            }

            foreach ($eventData['bookmakers'] as $bookmaker) {
                if ($bookmaker['key'] !== 'draftkings') {
                    continue; // Skip other bookmakers
                }

                $data = $this->prepareOddsData($eventData, $bookmaker, $homeTeam, $awayTeam);
                Log::info('Prepared odds data:', $data);

                $existingOdds = MlbOdds::where('event_id', $data['event_id'])
                    ->where('bookmaker_key', $data['bookmaker_key'])
                    ->first();

                if ($existingOdds) {
                    Log::info('Existing odds found for event ID: ' . $data['event_id']);

                    if ($this->hasOddsChanged($existingOdds, $data)) {
                        Log::info('Odds have changed for event ID: ' . $data['event_id']);
                        $this->storeHistoricalOdds($existingOdds);
                        $existingOdds->update($data);
                    }
                } else {
                    Log::info('No existing odds found for event ID: ' . $data['event_id']);
                    $this->storeOdds($data);
                }
            }
        }
    }

    private function prepareOddsData($event, $bookmaker, $homeTeam, $awayTeam)
    {
        $commenceTime = Carbon::parse($event['commence_time'])->setTimezone('America/Chicago')->format('Y-m-d H:i:s');
        $data = [
            'event_id' => $event['id'],
            'sport_title' => $event['sport_title'],
            'sport_key' => $event['sport_key'],
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'bookmaker_key' => $bookmaker['key'],
            'commence_time' => $commenceTime,
            'h2h_home_price' => null,
            'h2h_away_price' => null,
            'spread_home_point' => null,
            'spread_away_point' => null,
            'spread_home_price' => null,
            'spread_away_price' => null,
            'total_over_point' => null,
            'total_under_point' => null,
            'total_over_price' => null,
            'total_under_price' => null,
        ];

        foreach ($bookmaker['markets'] as $market) {
            foreach ($market['outcomes'] as $outcome) {
                if ($market['key'] == 'h2h') {
                    if ($outcome['name'] == $event['home_team']) {
                        $data['h2h_home_price'] = $outcome['price'];
                    } elseif ($outcome['name'] == $event['away_team']) {
                        $data['h2h_away_price'] = $outcome['price'];
                    }
                } elseif ($market['key'] == 'spreads') {
                    if ($outcome['name'] == $event['home_team']) {
                        $data['spread_home_point'] = $outcome['point'] ?? 0;
                        $data['spread_home_price'] = $outcome['price'];
                    } elseif ($outcome['name'] == $event['away_team']) {
                        $data['spread_away_point'] = $outcome['point'] ?? 0;
                        $data['spread_away_price'] = $outcome['price'];
                    }
                } elseif ($market['key'] == 'totals') {
                    if ($outcome['name'] == 'Over') {
                        $data['total_over_point'] = $outcome['point'] ?? 0;
                        $data['total_over_price'] = $outcome['price'];
                    } elseif ($outcome['name'] == 'Under') {
                        $data['total_under_point'] = $outcome['point'] ?? 0;
                        $data['total_under_price'] = $outcome['price'];
                    }
                }
            }
        }

        return $data;
    }

    private function storeOdds($data)
    {
        MlbOdds::create($data);
    }

    private function storeHistoricalOdds($existingOdds)
    {
        MlbOddsHistory::create([
            'odds_id' => $existingOdds->id,
            'h2h_home_price' => $existingOdds->h2h_home_price,
            'h2h_away_price' => $existingOdds->h2h_away_price,
            'spread_home_point' => $existingOdds->spread_home_point,
            'spread_away_point' => $existingOdds->spread_away_point,
            'spread_home_price' => $existingOdds->spread_home_price,
            'spread_away_price' => $existingOdds->spread_away_price,
            'total_over_point' => $existingOdds->total_over_point,
            'total_under_point' => $existingOdds->total_under_point,
            'total_over_price' => $existingOdds->total_over_price,
            'total_under_price' => $existingOdds->total_under_price,
            'commence_time' => $existingOdds->commence_time,
            'bookmaker_key' => $existingOdds->bookmaker_key,
            'created_at' => now(), // Manually set the created_at timestamp
        ]);
    }

    private function hasOddsChanged($existingOdds, $newData)
    {
        return $existingOdds->h2h_home_price != $newData['h2h_home_price'] ||
            $existingOdds->h2h_away_price != $newData['h2h_away_price'] ||
            $existingOdds->spread_home_point != $newData['spread_home_point'] ||
            $existingOdds->spread_away_point != $newData['spread_away_point'] ||
            $existingOdds->spread_home_price != $newData['spread_home_price'] ||
            $existingOdds->spread_away_price != $newData['spread_away_price'] ||
            $existingOdds->total_over_point != $newData['total_over_point'] ||
            $existingOdds->total_under_point != $newData['total_under_point'] ||
            $existingOdds->total_over_price != $newData['total_over_price'] ||
            $existingOdds->total_under_price != $newData['total_under_price'];
    }
}