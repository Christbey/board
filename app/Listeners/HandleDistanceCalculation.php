<?php

namespace App\Listeners;

use App\Events\CalculateStadiumDistance;
use App\Models\NflStadium;
use Illuminate\Support\Facades\Log;

class HandleDistanceCalculation
{
    /**
     * Handle the event.
     *
     * @param CalculateStadiumDistance $event
     * @return float|int|null
     */
    public function handle(CalculateStadiumDistance $event)
    {
        return $this->calculateStadiumDistance($event->homeTeamId, $event->awayTeamId);
    }

    /**
     * Calculate the distance between two stadiums.
     */
    protected function calculateStadiumDistance($homeTeamId, $awayTeamId): float|int|null
    {
        $homeStadium = NflStadium::where('team_id', $homeTeamId)->first();
        $awayStadium = NflStadium::where('team_id', $awayTeamId)->first();

        if ($homeStadium && $awayStadium) {
            // Call the calculateDistance method from the NflStadium model
            $distance = $homeStadium->calculateDistance(
                $homeStadium->latitude, $homeStadium->longitude,
                $awayStadium->latitude, $awayStadium->longitude
            );

            Log::info("Distance between stadiums: $distance kilometers");

            return $distance;
        } else {
            Log::warning("One or both stadiums not found for team IDs: $homeTeamId, $awayTeamId");
            return null;
        }
    }
}
