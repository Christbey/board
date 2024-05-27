<?php

namespace App\Listeners;

use App\Events\OddsFetched;
use App\Models\MlbOdds;
use App\Models\NflOdds;
use App\Models\NcaaOdds;
use App\Models\NbaOdds;
use App\Models\NflTeam;
use App\Models\MlbTeam;
use App\Models\NcaaTeam;
use App\Models\NbaTeam;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class StoreOdds
{
    public function handle(OddsFetched $event)
    {
        foreach ($event->odds as $eventData) {
            $teamModel = $this->getTeamModel($eventData['sport_key']);
            $oddsModel = $this->getOddsModel($eventData['sport_key']);

            $homeTeam = $this->getOrCreateTeam($teamModel, $eventData['home_team']);
            $awayTeam = $this->getOrCreateTeam($teamModel, $eventData['away_team']);

            foreach ($eventData['bookmakers'] as $bookmaker) {
                if ($bookmaker['key'] !== 'draftkings') {
                    continue;
                }

                $data = $this->prepareOddsData($eventData, $bookmaker, $homeTeam, $awayTeam);
                $this->storeOdds($oddsModel, $data);
            }
        }
    }

    private function getTeamModel($sportKey)
    {
        switch ($sportKey) {
            case 'americanfootball_nfl':
                return NflTeam::class;
            case 'baseball_mlb':
                return MlbTeam::class;
            case 'americanfootball_ncaaf':
                return NcaaTeam::class;
            case 'basketball_nba':
                return NbaTeam::class;
            default:
                throw new \Exception("Unknown sport key: {$sportKey}");
        }
    }

    private function getOddsModel($sportKey)
    {
        switch ($sportKey) {
            case 'americanfootball_nfl':
                return NflOdds::class;
            case 'baseball_mlb':
                return MlbOdds::class;
            case 'americanfootball_ncaaf':
                return NcaaOdds::class;
            case 'basketball_nba':
                return NbaOdds::class;
            default:
                throw new \Exception("Unknown sport key: {$sportKey}");
        }
    }

    private function getOrCreateTeam($teamModel, $teamName)
    {
        return $teamModel::firstOrCreate(['name' => $teamName], [
            'name' => $teamName,
            // Add other default values if needed
        ]);
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

    private function storeOdds($oddsModel, $data)
    {
        $oddsModel::updateOrCreate(
            ['event_id' => $data['event_id'], 'bookmaker_key' => $data['bookmaker_key']],
            $data
        );
    }
}
