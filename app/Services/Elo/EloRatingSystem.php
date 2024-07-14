<?php

namespace App\Services\Elo;

use App\Models\NflPrediction;
use App\Models\NflTeamSchedule;
use App\Models\NflPlayByPlay;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class EloRatingSystem
{
    private EloCalculator $eloCalculator;
    private TeamRatingManager $teamRatingManager;
    private QBRatingManager $qbRatingManager;
    private DistanceCalculator $distanceCalculator;
    private DataStorage $dataStorage;

    public function __construct()
    {
        $this->eloCalculator = new EloCalculator();
        $this->teamRatingManager = new TeamRatingManager();
        $this->qbRatingManager = new QBRatingManager();
        $this->distanceCalculator = new DistanceCalculator();
        $this->dataStorage = new DataStorage();

        $this->teamRatingManager->initializeRatings();
        $this->trainRatingsWithPastSeasons();
        $this->dataStorage->storeRatingsInDb($this->teamRatingManager->getRatings());
    }

    public function updateRatings($homeTeam, $awayTeam, $homeScore, $awayScore, $distance, $homeRested = false, $awayRested = false, $neutralSite = false, $noFans = false, $isPlayoff = false, $homeQbChange = false, $awayQbChange = false): array
    {
        $predictedHomePts = $this->calculatePredictedPoints($homeTeam, $awayTeam, $homeScore, $awayScore, $distance, $homeRested, $awayRested, $neutralSite, $noFans, $isPlayoff, $homeQbChange, $awayQbChange);
        $predictedAwayPts = $this->calculatePredictedPoints($awayTeam, $homeTeam, $awayScore, $homeScore, $distance, $awayRested, $homeRested, $neutralSite, $noFans, $isPlayoff, $awayQbChange, $homeQbChange);

        $homeWinPercentage = $this->calculateWinningPercentage($homeTeam, $awayTeam, $homeScore, $awayScore);
        $awayWinPercentage = 100 - $homeWinPercentage;

        $this->teamRatingManager->updateRatings($homeTeam, $awayTeam, $homeScore, $awayScore, $distance, $homeRested, $awayRested, $neutralSite, $noFans, $isPlayoff, $homeQbChange, $awayQbChange);

        return [
            'home_pts' => $predictedHomePts,
            'away_pts' => $predictedAwayPts,
            'home_win_percentage' => $homeWinPercentage,
            'away_win_percentage' => $awayWinPercentage,
        ];
    }

    private function calculatePredictedPoints($team1, $team2, $score1, $score2, $distance, $rested1, $rested2, $neutralSite, $noFans, $isPlayoff, $qbChange1, $qbChange2)
    {
        // Implement your logic to calculate the predicted points
        // Example:
        $predictedPoints = $score1 + 1; // Replace with actual prediction logic
        return $predictedPoints;
    }

    private function calculateWinningPercentage($homeTeam, $awayTeam, $homeScore, $awayScore)
    {
        // Implement your logic to calculate the winning percentage
        // Example:
        $homeWinPercentage = 50; // Replace with actual calculation logic
        return $homeWinPercentage;
    }

    public function trainRatingsWithPastSeasons(): void
    {
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
            $homeStadium = $game->homeStadium;
            $awayStadium = $game->awayStadium;

            $distance = $this->distanceCalculator->calculateDistance($homeStadium, $awayStadium);

            $predictedRatings = $this->updateRatings(
                $game->team_id_home,
                $game->team_id_away,
                $game->home_pts,
                $game->away_pts,
                $distance,
                false, // Example rested flag, replace with actual logic
                false, // Example rested flag, replace with actual logic
                false, // Example neutral site flag, replace with actual logic
                false, // Example no fans flag, replace with actual logic
                $game->season_type === 'playoff'
            );

            if ($predictedRatings === null) {
                \Log::error('Predicted ratings are null for game ID: ' . $game->game_id);
                continue;
            }

            if (!isset($predictedRatings['home_pts']) || !isset($predictedRatings['away_pts'])) {
                \Log::error('Missing keys in predicted ratings for game ID: ' . $game->game_id);
                continue;
            }

            //$this->logGamePredictions($game, $predictedRatings);
        }
    }

    public function logExpectedWinningPercentageAndPredictedScore($game, $homeStadium, $awayStadium): void
    {
        // Only process games with dates after 03-01-2024 and not in preseason
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

            $prediction = $this->getActualScorePrediction(
                $game->team_id_home,
                $game->team_id_away,
                $distance,
                false, // Example neutral site flag, replace with actual logic
                false, // Example no fans flag, replace with actual logic,
                $game->season_type === 'playoff'
            );

            $logMessage = sprintf(
                "Game ID: %s - Expected Winning Percentage for %s vs %s: Home: %.2f%%, Away: %.2f%%\n" .
                "Game ID: %s - Predicted Score: Home: %d (%.2f%%), Away: %d (%.2f%%)\n",
                $game->game_id, $game->team_id_home, $game->team_id_away,
                round($expectedHomeScore * 100, 2), round($expectedAwayScore * 100, 2),
                $game->game_id, $prediction['teamA'], round($expectedHomeScore * 100, 2), $prediction['teamB'], round($expectedAwayScore * 100, 2)
            );

            echo $logMessage;

            NflPrediction::createOrUpdate([
                'game_id' => $game->game_id,
                'team_id_home' => $game->team_id_home,
                'team_id_away' => $game->team_id_away,
                'game_date' => $game->game_date,
                'home_pts_prediction' => $prediction['teamA'],
                'away_pts_prediction' => $prediction['teamB'],
                'home_win_percentage' => round($expectedHomeScore * 100, 2),
                'away_win_percentage' => round($expectedAwayScore * 100, 2),
                'season_type' => $game->season_type,
            ]);
        }
    }

    private function logGamePredictions($game, $predictedRatings): void
    {
        $logMessage = sprintf(
            "Game ID: %s - Expected Winning Percentage for %s vs %s: Home: %.2f%%, Away: %.2f%%\n" .
            "Game ID: %s - Predicted Score: Home: %d (%.2f%%), Away: %d (%.2f%%)\n",
            $game->game_id, $game->team_id_home, $game->team_id_away,
            $predictedRatings['home_win_percentage'], $predictedRatings['away_win_percentage'],
            $game->game_id, $predictedRatings['home_pts'], $predictedRatings['home_win_percentage'], $predictedRatings['away_pts'], $predictedRatings['away_win_percentage']
        );

        echo $logMessage;
    }

    public function getActualScorePrediction($teamA, $teamB, $distance, $neutralSite = false, $noFans = false, $isPlayoff = false): array
    {
        $eloDiff = $this->teamRatingManager->getRatings()[$teamA] - $this->teamRatingManager->getRatings()[$teamB] + $this->eloCalculator->calculateHomeFieldAdjustment($teamA, $teamB, $distance, $neutralSite, $noFans);
        if ($isPlayoff) {
            $eloDiff *= $this->eloCalculator->playoffMultiplier;
        }
        $pointSpread = $this->eloCalculator->calculateExpectedPointSpread($eloDiff);
        $averagePoints = $this->eloCalculator->averagePoints;

        $expectedScoreA = $averagePoints + ($pointSpread / 2);
        $expectedScoreB = $averagePoints - ($pointSpread / 2);

        return [
            'teamA' => round($expectedScoreA),
            'teamB' => round($expectedScoreB)
        ];
    }

    public function getRatings(): array
    {
        return $this->teamRatingManager->getRatings();
    }

    public function calculateExpectedWinsForTeams(): array
    {
        $futureGames = NflTeamSchedule::whereNull('home_result')
            ->whereNull('away_result')
            ->where('game_status', 'scheduled')
            ->get();

        $expectedWins = array_fill_keys(array_keys($this->teamRatingManager->getRatings()), 0);

        foreach ($futureGames as $game) {
            $homeStadium = $game->homeStadium;
            $awayStadium = $game->awayStadium;

            $distance = $this->distanceCalculator->calculateDistance($homeStadium, $awayStadium);

            $expectedHomeWinProbability = $this->eloCalculator->calculateExpectedScore(
                $this->teamRatingManager->getRatings()[$game->team_id_home],
                $this->teamRatingManager->getRatings()[$game->team_id_away]
            );

            $expectedAwayWinProbability = 1 - $expectedHomeWinProbability;

            $expectedWins[$game->team_id_home] += $expectedHomeWinProbability;
            $expectedWins[$game->team_id_away] += $expectedAwayWinProbability;
        }

        return $expectedWins;
    }

    public function updateEPARatingsAfterGame($gameId): void
    {
        $game = NflTeamSchedule::find($gameId);

        $homeEPA = $this->calculateTotalEPAForTeam($game->team_id_home, $gameId);
        $awayEPA = $this->calculateTotalEPAForTeam($game->team_id_away, $gameId);

        $this->logEPARatings($game->team_id_home, $homeEPA, $game->team_id_away, $awayEPA);
    }

    public function calculateTotalEPAForTeam($teamId, $gameId): float|int
    {
        $plays = $this->getPlaysForTeamInGame($teamId, $gameId);

        $totalEPA = 0;
        foreach ($plays as $play) {
            $totalEPA += $this->calculateEPA($play);
        }

        return $totalEPA;
    }

    public function getPlaysForTeamInGame($teamId, $gameId): array
    {
        return NflPlayByPlay::where('team_id', $teamId)
            ->where('game_id', $gameId)
            ->select('play')
            ->get()
            ->toArray();
    }

    public function calculateEPA($playData): float
    {
        $startFieldPosition = $this->parseFieldPositionFromPlay($playData['play']);
        $endFieldPosition = $this->parseFieldPositionFromPlay($playData['play']);

        $expectedPointsBefore = $this->getExpectedPoints($startFieldPosition);
        $expectedPointsAfter = $this->getExpectedPoints($endFieldPosition);

        return $expectedPointsAfter - $expectedPointsBefore;
    }

    public function parseFieldPositionFromPlay($playDescription): int|string
    {
        if (preg_match('/to\s[A-Z]{2,3}\s(\d{1,2})/', $playDescription, $matches)) {
            return $matches[1]; // Return the yard line
        }

        return 0; // Fallback if extraction fails
    }

    public function parseFieldPositionFromDownAndDistance($downAndDistance): int|string
    {
        if (preg_match('/at\s[A-Z]{2,3}\s(\d{1,2})/', $downAndDistance, $matches)) {
            return $matches[1]; // Return the yard line
        }

        return 0; // Fallback if extraction fails
    }

    public function getExpectedPoints($fieldPosition): float
    {
        $expectedPointsTable = Config::get('nfl.expectedPointsTable');
        return $expectedPointsTable[$fieldPosition] ?? 0.0;
    }

    public function logEPARatings($homeTeamId, $homeEPA, $awayTeamId, $awayEPA): void
    {
        Log::info("EPA Ratings: Home Team ($homeTeamId): $homeEPA, Away Team ($awayTeamId): $awayEPA");
    }
}
