<?php

namespace App\Services;

use App\Models\NflPrediction;
use App\Models\NflTeamSchedule;
use App\Models\NflOdds;
use App\Services\Elo\EloRatingSystem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class NflPredictionService
{
    public EloRatingSystem $eloRatingSystem;

    public function __construct(EloRatingSystem $eloRatingSystem)
    {
        $this->eloRatingSystem = $eloRatingSystem;
    }

    public function logPredictedScores(): string
    {
        $cutoffDate = Carbon::createFromFormat('Y-m-d', '2024-03-01');
        $games = $this->getFutureGames($cutoffDate);
        $expectedWins = $this->initializeExpectedWins();

        foreach ($games as $game) {
            $homeStadium = $game->homeStadium;
            $awayStadium = $game->awayStadium;

            $odds = $this->getOdds($game->team_id_home, $game->team_id_away);
            $homeOdds = $odds['home'] ?? 0.0;
            $awayOdds = $odds['away'] ?? 0.0;

            if (!$odds) {
                $this->logMessage("Missing odds information for game ID {$game->game_id} between team {$game->team_id_home} and team {$game->team_id_away}. Using default odds.");
            } else {
                $this->logMessage("Logging odds for game ID {$game->game_id} between team {$game->team_id_home} and team {$game->team_id_away}.");
            }

            $this->logExpectedWinningPercentageAndPredictedScore($game, $homeStadium, $awayStadium, $homeOdds, $awayOdds, $expectedWins);
        }

        $this->logFinalExpectedWins($expectedWins);

        return 'Predicted scores logged successfully.';
    }

    private function getFutureGames($cutoffDate)
    {
        return NflTeamSchedule::whereNull('home_result')
            ->whereNull('away_result')
            ->where('game_status', 'scheduled')
            ->whereDate('game_date', '>', $cutoffDate)
            ->where('season_type', '<>', 'Preseason')
            ->get();
    }

    private function getOdds(int $homeTeamId, int $awayTeamId): array
    {
        $odds = NflOdds::where('home_team_id', $homeTeamId)
            ->where('away_team_id', $awayTeamId)
            ->first();

        if ($odds) {
            return [
                'home' => $odds->h2h_home_price ?? 0.0,
                'away' => $odds->h2h_away_price ?? 0.0,
            ];
        }

        Log::warning("Missing odds information for game between team $homeTeamId and team $awayTeamId. Using default odds.");
        return [];
    }

    private function logExpectedWinningPercentageAndPredictedScore(NflTeamSchedule $game, $homeStadium, $awayStadium, float $homeOdds, float $awayOdds, array &$expectedWins): void
    {
        if (is_null($game->home_result) && is_null($game->away_result) && $game->season_type !== 'Preseason') {
            $distance = $this->eloRatingSystem->distanceCalculator->calculateDistance($homeStadium, $awayStadium);

            $expectedHomeScore = $this->eloRatingSystem->eloCalculator->calculateExpectedScore(
                $this->eloRatingSystem->teamRatingManager->getRatings()[$game->team_id_home],
                $this->eloRatingSystem->teamRatingManager->getRatings()[$game->team_id_away]
            );
            $expectedAwayScore = 1 - $expectedHomeScore;

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

            $this->logMessage($logMessage);

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

            // Add to expected wins
            $expectedWins[$game->team_id_home] += $expectedHomeScore;
            $expectedWins[$game->team_id_away] += $expectedAwayScore;
        }
    }

    private function initializeExpectedWins(): array
    {
        $teamRatings = $this->eloRatingSystem->teamRatingManager->getRatings();
        return array_fill_keys(array_keys($teamRatings), 0);
    }

    private function logFinalExpectedWins(array $expectedWins)
    {
        Log::info('Final Expected Wins:', $expectedWins);
        foreach ($expectedWins as $teamId => $wins) {
            $this->logMessage("Team ID {$teamId}: " . round($wins, 2) . ' expected wins');
        }
    }

    private function logMessage($message)
    {
        Log::info($message);
        echo $message . "\n";
    }

    public function calculateExpectedWins()
    {
        // Add logic to calculate expected wins for all teams
    }

    public function calculateExpectedWinsForTeam($teamId)
    {
        $homeWins = NflPrediction::where('team_id_home', $teamId)->sum('home_win_percentage');
        $awayWins = NflPrediction::where('team_id_away', $teamId)->sum('away_win_percentage');
        return ($homeWins + $awayWins) / 100;
    }
}
