<?php

namespace App\Traits;

use Carbon\Carbon;

trait PreparesOddsData
{
    /**
     * Prepare odds data from event and bookmaker.
     *
     * @param  array  $event
     * @param  array  $bookmaker
     * @param  object  $homeTeam
     * @param  object  $awayTeam
     * @param  bool  $isLive
     * @return array
     */
    public function prepareOddsData(array $event, array $bookmaker, $homeTeam, $awayTeam, bool $isLive): array
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
            'is_live' => $isLive,
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
}
