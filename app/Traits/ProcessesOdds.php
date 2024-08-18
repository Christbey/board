<?php

namespace App\Traits;

use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Helpers\DiscordHelper;
use App\Notifications\DiscordNotification;

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

                    $existingOdds = $this->storeOrUpdateOdds($oddsModel, $oddsData);

                    if ($existingOdds && $this->oddsHaveChanged($existingOdds, $oddsData)) {
                        try {
                            $description = "**Odds have changed!**\n";

                            if (number_format($existingOdds->total_over_point, 2) !== number_format($oddsData['total_over_point'], 2)) {
                                $emoji = $oddsData['total_over_point'] > $existingOdds->total_over_point ? '⬆️' : '⬇️';
                                $description .= "~~Old Total: {$existingOdds->total_over_point}~~ ➔ New Total: {$oddsData['total_over_point']} {$emoji}\n";
                            }

                            if (number_format($existingOdds->h2h_home_price, 2) !== number_format($oddsData['h2h_home_price'], 2)) {
                                $description .= "~~Old Home Team H2H Price: {$existingOdds->h2h_home_price}~~ ➔ New Home Team H2H Price: {$oddsData['h2h_home_price']}\n";
                            }

                            if (number_format($existingOdds->h2h_away_price, 2) !== number_format($oddsData['h2h_away_price'], 2)) {
                                $description .= "~~Old Away Team H2H Price: {$existingOdds->h2h_away_price}~~ ➔ New Away Team H2H Price: {$oddsData['h2h_away_price']}\n";
                            }

                            if (number_format($existingOdds->spread_home_point, 2) !== number_format($oddsData['spread_home_point'], 2)) {
                                $description .= "~~Old Home Spread Point: {$existingOdds->spread_home_point}~~ ➔ New Home Spread Point: {$oddsData['spread_home_point']}\n";
                            }

                            if (number_format($existingOdds->spread_away_point, 2) !== number_format($oddsData['spread_away_point'], 2)) {
                                $description .= "~~Old Away Spread Point: {$existingOdds->spread_away_point}~~ ➔ New Away Spread Point: {$oddsData['spread_away_point']}\n";
                            }

                            if (!empty(trim($description))) {
                                // Use DiscordHelper to build the Discord message
                                $message = (new DiscordHelper())
                                    ->setTitle("{$homeTeam->name} {$oddsData['spread_home_point']} vs {$awayTeam->name} {$oddsData['spread_away_point']}")
                                    ->setDescription($description)
                                    ->setColor('#E77625')
                                    ->build();

                                // Send the notification using the built DiscordMessage
                                $this->notifyDiscord($message);
                            }

                            Log::info('Notification sent to Discord successfully');
                        } catch (Exception $e) {
                            Log::error('Failed to send notification to Discord', ['error' => $e->getMessage()]);
                        }

                        Log::info('Odds have changed, storing history for event ID: ' . $oddsData['event_id']);
                        $this->storeOddsHistory($oddsHistoryModel, $existingOdds, $oddsData);
                        $existingOdds->update($oddsData);
                    }
                }
            }
        }
    }

    protected function notifyDiscord($message)
    {
        $user = User::find(1); // Retrieve the user to send the notification
        $user?->notify(new DiscordNotification($message));
    }

    protected function getTeam($teamModel, $teamName)
    {
        return $teamModel::firstWhere('name', $teamName);
    }

    protected function prepareOddsData(array $event, array $bookmaker, $homeTeamId, $awayTeamId): array
    {
        return [
            'event_id' => $event['id'],
            'sport_key' => $event['sport_key'],
            'sport_title' => $event['sport_title'],
            'commence_time' => Carbon::parse($event['commence_time'])->setTimezone('America/Chicago')->format('Y-m-d H:i:s'),
            'home_team_id' => $homeTeamId,
            'away_team_id' => $awayTeamId,
            'bookmaker_key' => $bookmaker['key'],
            'h2h_home_price' => $this->getPrice($bookmaker, 'h2h', $event['home_team']),
            'h2h_away_price' => $this->getPrice($bookmaker, 'h2h', $event['away_team']),
            'spread_home_point' => $this->getPoint($bookmaker, 'spreads', $event['home_team']),
            'spread_away_point' => $this->getPoint($bookmaker, 'spreads', $event['away_team']),
            'spread_home_price' => $this->getPrice($bookmaker, 'spreads', $event['home_team']),
            'spread_away_price' => $this->getPrice($bookmaker, 'spreads', $event['away_team']),
            'total_over_point' => $this->getPoint($bookmaker, 'totals', 'Over'),
            'total_under_point' => $this->getPoint($bookmaker, 'totals', 'Under'),
            'total_over_price' => $this->getPrice($bookmaker, 'totals', 'Over'),
            'total_under_price' => $this->getPrice($bookmaker, 'totals', 'Under'),
            'last_update' => isset($event['last_update'])
                ? Carbon::parse($event['last_update'])->setTimezone('America/Chicago')->format('Y-m-d H:i:s')
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
