<?php

namespace App\Listeners;

use App\Traits\CompareOdds;
use App\Traits\PreparesOddsData;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

abstract class BaseOddsListener
{
    use CompareOdds;
    use PreparesOddsData;

    protected function handleOdds($event, $modelClass, $historyModelClass, $teamClass)
    {
        Log::info(class_basename($this) . ' listener triggered.');

        foreach ($event->odds as $eventData) {
            $homeTeam = $teamClass::firstOrCreate(['name' => $eventData['home_team']]);
            $awayTeam = $teamClass::firstOrCreate(['name' => $eventData['away_team']]);

            if (!$homeTeam || !$awayTeam) {
                Log::warning("Teams not found: Home - {$eventData['home_team']}, Away - {$eventData['away_team']}");
                continue; // Skip if teams are not found
            }

            $commenceTime = Carbon::parse($eventData['commence_time'])->setTimezone('America/Chicago');
            $currentTime = Carbon::now('America/Chicago');
            $isLive = $commenceTime->lessThanOrEqualTo($currentTime);

            foreach ($eventData['bookmakers'] as $bookmaker) {
                if ($bookmaker['key'] !== 'draftkings') {
                    continue; // Skip other bookmakers
                }

                $data = $this->prepareOddsData($eventData, $bookmaker, $homeTeam, $awayTeam, $isLive);
                Log::info('Prepared odds data:', $data);

                $existingOdds = $modelClass::where('event_id', $data['event_id'])
                    ->where('bookmaker_key', $data['bookmaker_key'])
                    ->first();

                if ($existingOdds) {
                    Log::info('Existing odds found for event ID: ' . $data['event_id']);

                    if ($this->oddsHaveChanged($existingOdds, $data)) {
                        Log::info('Odds have changed for event ID: ' . $data['event_id']);
                        $this->storeHistoricalOdds($existingOdds, $historyModelClass);
                        $existingOdds->update($data);
                    }
                } else {
                    Log::info('No existing odds found for event ID: ' . $data['event_id']);
                    $this->storeOdds($data, $modelClass);
                }
            }
        }
    }

    protected function storeOdds($data, $modelClass)
    {
        $modelClass::create($data);
    }

    protected function storeHistoricalOdds($existingOdds, $historyModelClass)
    {
        $historyModelClass::create([
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
}
