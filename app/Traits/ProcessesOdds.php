<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

trait ProcessesOdds
{
    use CompareOdds;

    public function processOdds(array $odds, $teamModel, $oddsModel, $oddsHistoryModel)
    {
        foreach ($odds as $odd) {
            $homeTeam = $this->getTeam($teamModel, $odd['home_team']);
            $awayTeam = $this->getTeam($teamModel, $odd['away_team']);

            if ($homeTeam && $awayTeam) {
                foreach ($odd['bookmakers'] as $bookmaker) {
                    if ($bookmaker['key'] !== 'draftkings') {
                        continue; // Skip other bookmakers
                    }

                    $oddsData = $this->prepareOddsData($odd, $bookmaker, $homeTeam->id, $awayTeam->id);
                    Log::info('Prepared odds data:', $oddsData);

                    $existingOdds = $this->storeOrUpdateOdds($oddsModel, $oddsData);

                    if ($existingOdds && $this->oddsHaveChanged($existingOdds, $oddsData)) {
                        Log::info('Odds have changed, storing history for event ID: ' . $oddsData['event_id']);
                        $this->storeOddsHistory($oddsHistoryModel, $existingOdds, $oddsData);
                        $existingOdds->update($oddsData);
                    } elseif (!$existingOdds) {
                        Log::info('Storing new odds for event ID: ' . $oddsData['event_id']);
                        $this->storeOdds($oddsModel, $oddsData);
                    }
                }
            }
        }
    }

    protected function getTeam($teamModel, $teamName)
    {
        return $teamModel::firstWhere('name', $teamName);
    }

    protected function prepareOddsData(array $odd, array $bookmaker, $homeTeamId, $awayTeamId)
    {
        return [
            'event_id' => $odd['id'],
            'sport_key' => $odd['sport_key'],
            'sport_title' => $odd['sport_title'],
            'commence_time' => Carbon::parse($odd['commence_time'])->setTimezone('America/Chicago')->format('Y-m-d H:i:s'),
            'home_team_id' => $homeTeamId,
            'away_team_id' => $awayTeamId,
            'bookmaker_key' => $bookmaker['key'],
            'h2h_home_price' => $this->getPrice($bookmaker, 'h2h', $odd['home_team']),
            'h2h_away_price' => $this->getPrice($bookmaker, 'h2h', $odd['away_team']),
            'spread_home_point' => $this->getPoint($bookmaker, 'spreads', $odd['home_team']),
            'spread_away_point' => $this->getPoint($bookmaker, 'spreads', $odd['away_team']),
            'spread_home_price' => $this->getPrice($bookmaker, 'spreads', $odd['home_team']),
            'spread_away_price' => $this->getPrice($bookmaker, 'spreads', $odd['away_team']),
            'total_over_point' => $this->getPoint($bookmaker, 'totals', 'Over'),
            'total_under_point' => $this->getPoint($bookmaker, 'totals', 'Under'),
            'total_over_price' => $this->getPrice($bookmaker, 'totals', 'Over'),
            'total_under_price' => $this->getPrice($bookmaker, 'totals', 'Under'),
            'last_update' => isset($odd['last_update'])
                ? Carbon::parse($odd['last_update'])->setTimezone('America/Chicago')->format('Y-m-d H:i:s')
                : null,
        ];
    }

    protected function getPrice(array $bookmaker, $marketKey, $outcomeName)
    {
        foreach ($bookmaker['markets'] as $market) {
            if ($market['key'] === $marketKey) {
                foreach ($market['outcomes'] as $outcome) {
                    if ($outcome['name'] === $outcomeName) {
                        return $outcome['price'] ?? null;
                    }
                }
            }
        }
        return null;
    }

    protected function getPoint(array $bookmaker, $marketKey, $outcomeName)
    {
        foreach ($bookmaker['markets'] as $market) {
            if ($market['key'] === $marketKey) {
                foreach ($market['outcomes'] as $outcome) {
                    if ($outcome['name'] === $outcomeName) {
                        return $outcome['point'] ?? null;
                    }
                }
            }
        }
        return null;
    }

    protected function storeOrUpdateOdds($oddsModel, array $oddsData)
    {
        $existingOdds = $oddsModel::where('event_id', $oddsData['event_id'])
            ->where('bookmaker_key', $oddsData['bookmaker_key'])
            ->first();

        if ($existingOdds) {
            return $existingOdds;
        }

        return $oddsModel::create($oddsData);
    }

    protected function storeOddsHistory($oddsHistoryModel, $existingOdds, array $oddsData)
    {
        Log::info('Storing historical odds:', ['odds_id' => $existingOdds->id]);

        $oddsHistoryModel::create([
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
