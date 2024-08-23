<?php

// Assuming NflOdd is the model for your odds table

namespace App\Services;

use App\Models\NflOdds;

class NflOddsService
{
    public function getOdds($eventId = null)
    {
        if ($eventId) {
            return NflOdds::where('event_id', $eventId)->first();
        }

        return NflOdds::all();
    }
}


