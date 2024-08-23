<?php

namespace App\Listeners;

use App\Events\CalculateExpectedScores;

class HandleExpectedScoreCalculation
{
    protected int $homeFieldAdvantage = 55; // Example value for home-field advantage

    public function handle(CalculateExpectedScores $event)
    {
        $homeTeamElo = $event->homeTeamElo + $this->homeFieldAdvantage;
        $awayTeamElo = $event->awayTeamElo;

        $scalingFactor = 750; // Adjust this as needed
        $eloDifference = $awayTeamElo - $homeTeamElo;

        $expectedHomeScore = 1 / (1 + pow(10, $eloDifference / $scalingFactor));
        $expectedAwayScore = 1 / (1 + pow(10, -$eloDifference / $scalingFactor));

        return [
            'home' => $expectedHomeScore,
            'away' => $expectedAwayScore,
        ];
    }
}
