<?php

namespace App\Traits;

use Carbon\Carbon;

trait ProcessesOdds
{
    public function processOdds(array $odds, $teamModel, $oddsModel, $oddsHistoryModel)
    {
        foreach ($odds as $odd) {
            $homeTeam = $this->getTeam($teamModel, $odd['home_team']);
            $awayTeam = $this->getTeam($teamModel, $odd['away_team']);

            if ($homeTeam && $awayTeam) {
                $oddsData = $this->prepareOddsData($odd, $homeTeam->id, $awayTeam->id);
                $this->storeOrUpdateOdds($oddsModel, $oddsHistoryModel, $oddsData);
            }
        }
    }

    protected function getTeam($teamModel, $teamName)
    {
        return $teamModel::firstOrCreate(['name' => $teamName]);
    }

    protected function prepareOddsData(array $odd, $homeTeamId, $awayTeamId)
    {
        return [
            'event_id' => $odd['id'],
            'sport_key' => $odd['sport_key'],
            'sport_title' => $odd['sport_title'],
            'commence_time' => Carbon::parse($odd['commence_time'])->setTimezone('America/Chicago')->format('Y-m-d H:i:s'),
            'home_team_id' => $homeTeamId,
            'away_team_id' => $awayTeamId,
            'bookmaker_key' => $odd['bookmakers'][0]['key'] ?? null,
            'h2h_home_price' => $this->getPrice($odd, 'h2h', $odd['home_team']),
            'h2h_away_price' => $this->getPrice($odd, 'h2h', $odd['away_team']),
            'spread_home_point' => $this->getPoint($odd, 'spreads', $odd['home_team']),
            'spread_away_point' => $this->getPoint($odd, 'spreads', $odd['away_team']),
            'spread_home_price' => $this->getPrice($odd, 'spreads', $odd['home_team']),
            'spread_away_price' => $this->getPrice($odd, 'spreads', $odd['away_team']),
            'total_over_point' => $this->getPoint($odd, 'totals', 'Over'),
            'total_under_point' => $this->getPoint($odd, 'totals', 'Under'),
            'total_over_price' => $this->getPrice($odd, 'totals', 'Over'),
            'total_under_price' => $this->getPrice($odd, 'totals', 'Under'),
            'last_update' => isset($odd['last_update'])
                ? Carbon::parse($odd['last_update'])->setTimezone('America/Chicago')->format('Y-m-d H:i:s')
                : null,
        ];
    }

    protected function getPrice(array $odd, $marketKey, $outcomeName)
    {
        if (isset($odd['bookmakers'][0]['markets'])) {
            foreach ($odd['bookmakers'][0]['markets'] as $market) {
                if ($market['key'] === $marketKey) {
                    foreach ($market['outcomes'] as $outcome) {
                        if ($outcome['name'] === $outcomeName) {
                            return $outcome['price'] ?? null;
                        }
                    }
                }
            }
        }
        return null;
    }

    protected function getPoint(array $odd, $marketKey, $outcomeName)
    {
        if (isset($odd['bookmakers'][0]['markets'])) {
            foreach ($odd['bookmakers'][0]['markets'] as $market) {
                if ($market['key'] === $marketKey) {
                    foreach ($market['outcomes'] as $outcome) {
                        if ($outcome['name'] === $outcomeName) {
                            return $outcome['point'] ?? null;
                        }
                    }
                }
            }
        }
        return null;
    }

    protected function storeOrUpdateOdds($oddsModel, $oddsHistoryModel, array $oddsData)
    {
        $existingOdds = $oddsModel::where('event_id', $oddsData['event_id'])
            ->where('bookmaker_key', $oddsData['bookmaker_key'])
            ->first();

        if ($existingOdds) {
            if ($this->oddsHaveChanged($existingOdds, $oddsData)) {
                $this->storeHistoricalOdds($existingOdds, $oddsHistoryModel);
                $existingOdds->update($oddsData);
            }
        } else {
            $oddsModel::create($oddsData);
        }
    }

    protected function storeHistoricalOdds($existingOdds, $oddsHistoryModel)
    {
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
            'created_at' => now(),
        ]);
    }
}
