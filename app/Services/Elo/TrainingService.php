<?php

namespace App\Services\Elo;

use App\Models\NflTeamSchedule;
use App\Models\NflOdds;
use Illuminate\Support\Facades\Log;

class TrainingService
{
    private DistanceCalculator $distanceCalculator;
    private EloRatingSystem $eloRatingSystem;

    public function __construct(DistanceCalculator $distanceCalculator, EloRatingSystem $eloRatingSystem)
    {
        $this->distanceCalculator = $distanceCalculator;
        $this->eloRatingSystem = $eloRatingSystem;
    }

    public function trainRatingsWithPastSeasons(): void
    {
        $restedFlagService = new RestedFlagService();

        $pastSeasons = NflTeamSchedule::where(function ($query) {
            $query->whereYear('game_date', '<', now()->year)
                ->orWhere(function ($query) {
                    $query->whereYear('game_date', now()->year)
                        ->whereDate('game_date', '<=', now()->toDateString());
                });
        })
            ->whereNotNull('home_result')
            ->whereNotNull('away_result')
            ->whereNotIn('season_type', ['preseason', 'postseason'])
            ->get();

        foreach ($pastSeasons as $game) {
            $distance = $this->distanceCalculator->calculateDistance($game->homeStadium, $game->awayStadium);

            $homeRested = $restedFlagService->isTeamRested($game->team_id_home, $game->game_date);
            $awayRested = $restedFlagService->isTeamRested($game->team_id_away, $game->game_date);

            // Fetch odds for the game using the composite key
            $odds = NflOdds::where('composite_key', $game->composite_key)->first();
            $homeOdds = $odds ? $odds->h2h_home_price : 0.0;
            $awayOdds = $odds ? $odds->h2h_away_price : 0.0;

            $predictedRatings = $this->eloRatingSystem->updateRatings(
                $game->team_id_home,
                $game->team_id_away,
                $game->home_pts,
                $game->away_pts,
                $distance,
                $homeRested,
                $awayRested,
                false,
                false,
                $game->season_type === 'Playoff',
                false,
                false,
                $homeOdds ?? 0.0,
                $awayOdds ?? 0.0
            );

            if (!isset($predictedRatings['home_pts']) || !isset($predictedRatings['away_pts'])) {
                Log::error('Missing keys in predicted ratings for game ID: ' . $game->game_id);
                continue;
            }

            // $this->eloRatingSystem->logGamePredictions($game, $predictedRatings);
        }
    }
}
