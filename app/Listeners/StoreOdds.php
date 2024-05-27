<?php

namespace App\Listeners;

use App\Events\OddsFetched;
use App\Models\NflTeam;
use App\Models\NcaaTeam;
use App\Models\Odds;
use App\Models\OddsHistory;
use Carbon\Carbon;

class StoreOdds
{
    public function handle(OddsFetched $event)
    {
        foreach ($event->odds as $eventData) {
            $homeTeam = $this->getTeamBySport($eventData['sport_key'], $eventData['home_team']);
            $awayTeam = $this->getTeamBySport($eventData['sport_key'], $eventData['away_team']);

            if (!$homeTeam || !$awayTeam) {
                continue; // Skip if teams are not found
            }

            foreach ($eventData['bookmakers'] as $bookmaker) {
                if ($bookmaker['key'] !== 'draftkings') {
                    continue; // Skip other bookmakers
                }

                $data = $this->prepareOddsData($eventData, $bookmaker, $homeTeam, $awayTeam);
                $this->storeOddsAndHistory($eventData['id'], $data);
            }
        }
    }

    private function getTeamBySport($sportKey, $teamName)
    {
        if ($sportKey === 'americanfootball_nfl') {
            return NflTeam::where('name', $teamName)->first();
        } elseif ($sportKey === 'americanfootball_ncaaf') {
            return NcaaTeam::where('name', $teamName)->first();
        }
        return null;
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
        ];

        foreach ($bookmaker['markets'] as $market) {
            foreach ($market['outcomes'] as $outcome) {
                $this->processMarketData($market, $outcome, $event, $data);
            }
        }

        return $data;
    }

    private function processMarketData($market, $outcome, $event, &$data)
    {
        if ($market['key'] == 'h2h') {
            $this->processH2HMarket($outcome, $event, $data);
        } elseif ($market['key'] == 'spreads') {
            $this->processSpreadMarket($outcome, $event, $data);
        } elseif ($market['key'] == 'totals') {
            $this->processTotalMarket($outcome, $data);
        }
    }

    private function processH2HMarket($outcome, $event, &$data)
    {
        if ($outcome['name'] == $event['home_team']) {
            $data['h2h_home_price'] = $outcome['price'];
        } elseif ($outcome['name'] == $event['away_team']) {
            $data['h2h_away_price'] = $outcome['price'];
        }
    }

    private function processSpreadMarket($outcome, $event, &$data)
    {
        if ($outcome['name'] == $event['home_team']) {
            $data['spread_home_point'] = $outcome['point'] ?? 0;
            $data['spread_home_price'] = $outcome['price'];
        } elseif ($outcome['name'] == $event['away_team']) {
            $data['spread_away_point'] = $outcome['point'] ?? 0;
            $data['spread_away_price'] = $outcome['price'];
        }
    }

    private function processTotalMarket($outcome, &$data)
    {
        if ($outcome['name'] == 'Over') {
            $data['total_over_point'] = $outcome['point'] ?? 0;
            $data['total_over_price'] = $outcome['price'];
        } elseif ($outcome['name'] == 'Under') {
            $data['total_under_point'] = $outcome['point'] ?? 0;
            $data['total_under_price'] = $outcome['price'];
        }
    }

    private function storeOddsAndHistory($eventId, $data)
    {
        $latestOdds = Odds::updateOrCreate(
            ['event_id' => $eventId],
            $data
        );

        $latestHistory = OddsHistory::where('odds_id', $latestOdds->id)->latest()->first();

        if ($this->hasOddsChanged($latestHistory, $latestOdds)) {
            OddsHistory::create([
                'odds_id' => $latestOdds->id,
                'h2h_home_price' => $latestOdds->h2h_home_price,
                'h2h_away_price' => $latestOdds->h2h_away_price,
                'spread_home_point' => $latestOdds->spread_home_point,
                'spread_away_point' => $latestOdds->spread_away_point,
                'spread_home_price' => $latestOdds->spread_home_price,
                'spread_away_price' => $latestOdds->spread_away_price,
                'total_over_point' => $latestOdds->total_over_point,
                'total_under_point' => $latestOdds->total_under_point,
                'total_over_price' => $latestOdds->total_over_price,
                'total_under_price' => $latestOdds->total_under_price,
            ]);
        }
    }

    private function hasOddsChanged($latestHistory, $latestOdds)
    {
        return !$latestHistory ||
            $latestHistory->h2h_home_price != $latestOdds->h2h_home_price ||
            $latestHistory->h2h_away_price != $latestOdds->h2h_away_price ||
            $latestHistory->spread_home_point != $latestOdds->spread_home_point ||
            $latestHistory->spread_away_point != $latestOdds->spread_away_point ||
            $latestHistory->spread_home_price != $latestOdds->spread_home_price ||
            $latestHistory->spread_away_price != $latestOdds->spread_away_price ||
            $latestHistory->total_over_point != $latestOdds->total_over_point ||
            $latestHistory->total_under_point != $latestOdds->total_under_point ||
            $latestHistory->total_over_price != $latestOdds->total_over_price ||
            $latestHistory->total_under_price != $latestOdds->total_under_price;
    }
}
