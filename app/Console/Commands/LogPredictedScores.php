<?php

namespace App\Console\Commands;

use App\Models\NflOdds;
use Illuminate\Console\Command;
use App\Models\NflTeamSchedule;
use App\Models\NflPrediction;
use App\Services\Elo\EloRatingSystem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class LogPredictedScores extends Command
{
    protected $signature = 'log:predicted-scores';
    protected $description = 'Log expected winning percentage and predicted scores for NFL games';

    private EloRatingSystem $eloRatingSystem;

    public function __construct(EloRatingSystem $eloRatingSystem)
    {
        parent::__construct();
        $this->eloRatingSystem = $eloRatingSystem;
    }

    public function handle()
    {
        $cutoffDate = Carbon::createFromFormat('Y-m-d', '2024-03-01');

        $games = NflTeamSchedule::whereNull('home_result')
            ->whereNull('away_result')
            ->where('game_status', 'scheduled')
            ->whereDate('game_date', '>', $cutoffDate)
            ->where('season_type', '<>', 'Preseason')
            ->get();

        foreach ($games as $game) {
            $homeStadium = $game->homeStadium; // Assuming you have these relationships
            $awayStadium = $game->awayStadium;

            $this->logExpectedWinningPercentageAndPredictedScore($game, $homeStadium, $awayStadium);
        }

        $this->info('Predicted scores logged successfully.');
    }

    public function logExpectedWinningPercentageAndPredictedScore(NflTeamSchedule $game, $homeStadium, $awayStadium): void
    {
        $cutoffDate = Carbon::createFromFormat('Y-m-d', '2024-03-01');

        if (is_null($game->home_result) && is_null($game->away_result) &&
            Carbon::parse($game->game_date)->greaterThan($cutoffDate) &&
            $game->season_type !== 'Preseason') {

            $distance = $this->eloRatingSystem->distanceCalculator->calculateDistance($homeStadium, $awayStadium);

            $expectedHomeScore = $this->eloRatingSystem->eloCalculator->calculateExpectedScore(
                $this->eloRatingSystem->teamRatingManager->getRatings()[$game->team_id_home],
                $this->eloRatingSystem->teamRatingManager->getRatings()[$game->team_id_away]
            );
            $expectedAwayScore = 1 - $expectedHomeScore;

            // Fetch odds for the game using the composite key
            $odds = NflOdds::where('composite_key', $game->composite_key)->first();
            $homeOdds = $odds ? (float)$odds->h2h_home_price : 0.0;
            $awayOdds = $odds ? (float)$odds->h2h_away_price : 0.0;

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

            Log::info($logMessage);

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
