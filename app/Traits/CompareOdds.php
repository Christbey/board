<?php

namespace App\Traits;

trait CompareOdds
{
    /**
     * Compare existing odds with new data.
     *
     * @param object $existingOdds
     * @param array $newData
     * @return bool
     */
    public function oddsHaveChanged($existingOdds, array $newData): bool
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
