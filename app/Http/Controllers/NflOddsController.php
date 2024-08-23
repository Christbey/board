<?php

namespace App\Http\Controllers;

use App\Events\CalculateEloRating;
use App\Events\CalculateExpectedScores;
use App\Events\CalculateStadiumDistance;
use App\Models\NflOdds;
use Illuminate\Support\Facades\Log;

class NflOddsController extends Controller
{
    protected int $homeFieldAdvantage = 55; // Example value for home-field advantage

    public function showOdds($eventId = null)
    {
        $odds = $this->getOdds($eventId);

        if ($odds) {
            // Trigger the Elo rating calculation events
            $homeTeamElo = event(new CalculateEloRating($odds->home_team_id, 'Regular Season'))[0];
            $awayTeamElo = event(new CalculateEloRating($odds->away_team_id, 'Regular Season'))[0];

            // Adjust for home field advantage
            $homeTeamElo += $this->homeFieldAdvantage;

            // Trigger the expected scores calculation event
            $expectedScores = event(new CalculateExpectedScores($homeTeamElo, $awayTeamElo, $odds->home_team_id, $odds->away_team_id))[0];

            return response()->json([
                'odds' => $odds,
                'expected_scores' => $expectedScores,
            ]);
        }

        return response()->json(['error' => 'Odds not found'], 404);
    }

    protected function getOdds($eventId = null)
    {
        return $eventId ? NflOdds::where('event_id', $eventId)->first() : NflOdds::all();
    }
}
