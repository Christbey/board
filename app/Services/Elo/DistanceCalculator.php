<?php

namespace App\Services\Elo;

class DistanceCalculator
{
    public function calculateDistance($homeStadium, $awayStadium): float|int
    {
        if ($homeStadium && $awayStadium) {
            return $this->haversineGreatCircleDistance(
                $homeStadium->latitude,
                $homeStadium->longitude,
                $awayStadium->latitude,
                $awayStadium->longitude
            );
        }
        return 0; // Fallback if stadium data is missing
    }

    private function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371): float|int
    {
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }
}
