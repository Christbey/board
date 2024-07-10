<?php

namespace App\Services\Elo;

use App\Models\EloRating;

class DataStorage
{
    public function storeRatingsInDb(array $ratings): void
    {
        foreach ($ratings as $teamId => $rating) {
            EloRating::updateOrCreate(
                ['team_id' => $teamId],
                ['rating' => $rating]
            );
        }
    }
}
