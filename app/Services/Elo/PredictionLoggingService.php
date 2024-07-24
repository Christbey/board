<?php

namespace App\Services\Elo;

use App\Models\NflPrediction;
use App\Models\NflTeamSchedule;
use App\Models\NflOdds;
use Carbon\Carbon;

class PredictionLoggingService
{
    private DistanceCalculator $distanceCalculator;
    private EloCalculator $eloCalculator;
    private TeamRatingManager $teamRatingManager;
    private EloRatingSystem $eloRatingSystem;

    public function __construct(
        DistanceCalculator $distanceCalculator,
        EloCalculator      $eloCalculator,
        TeamRatingManager  $teamRatingManager,
        EloRatingSystem    $eloRatingSystem
    )
    {
        $this->distanceCalculator = $distanceCalculator;
        $this->eloCalculator = $eloCalculator;
        $this->teamRatingManager = $teamRatingManager;
        $this->eloRatingSystem = $eloRatingSystem;
    }

    public function logExpectedWinningPercentageAndPredictedScore(NflTeamSchedule $game, $homeStadium, $awayStadium): void
    {
        $cutoffDate = Carbon::createFromFormat('Y-m-d', '2024-03-01');

        if (is_null($game->home_result) && is_null($game->away_result) &&
            Carbon::parse($game->game_date)->greaterThan($cutoffDate) &&
            $game->season_type !== 'Preseason') {

            $distance = $this->distanceCalculator->calculateDistance($homeStadium, $awayStadium);

            $expectedHomeScore = $this->eloCalculator->calculateExpectedScore(
                $this->teamRatingManager->getRatings()[$game->team_id_home],
                $this->teamRatingManager->getRatings()[$game->team_id_away]
            );
            $expectedAwayScore = 1 - $expectedHomeScore;

            // Fetch odds for the game using the composite key
            $odds = NflOdds::where('composite_key', $game->composite_key)->first();
            $homeOdds = $odds->h2h_home_price ?? 0.0;
            $awayOdds = $odds->h2h_away_price ?? 0.0;

            $prediction = $this->eloRatingSystem->getActualScorePrediction(
                $game->team_id_home,
                $game->team_id_away,
                $distance,
                false,
                false,
                $game->season_type === 'Playoff',
                $homeOdds,
                $awayOdds
            );

            $logMessage = sprintf(
                "Game ID: %s - Expected Winning Percentage for %s vs %s: Home: %.2f%%, Away: %.2f%%\n" .
                "Game ID: %s - Predicted Score: Home: %d (%.2f%%), Away: %d (%.2f%%)\n",
                $game->game_id, $game->team_id_home, $game->team_id_away,
                round($expectedHomeScore * 100, 2), round($expectedAwayScore * 100, 2),
                $game->game_id, $prediction['teamA'], round($expectedHomeScore * 100, 2), $prediction['teamB'], round($expectedAwayScore * 100, 2)
            );

            echo $logMessage;

            NflPrediction::updateOrCreate(
                ['game_id' => $game->game_id],
                [
                    'team_id_home' => $game->team_id_home,
                    'team_id_away' => $game->team_id_away,
                    'game_date' => $game->game_date,
                    'home_pts_prediction' => $prediction['teamA'],
                    'away_pts_prediction' => $prediction['teamB'],
                    'home_win_percentage' => round($expectedHomeScore * 100, 2),
                    'away_win_percentage' => round($expectedAwayScore * 100, 2),
                    'season_type' => $game->season_type,
                ]
            );
        }
    }
}
