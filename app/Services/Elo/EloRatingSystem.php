<?php

namespace App\Services\Elo;

use App\Models\NflTeamSchedule;
use App\Models\NflPlayByPlay;
use Illuminate\Support\Facades\DB;
use Log;

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

    public function updateRatings($homeTeam, $awayTeam, $homeScore, $awayScore, $distance, $homeRested = false, $awayRested = false, $neutralSite = false, $noFans = false, $isPlayoff = false, $homeQbChange = false, $awayQbChange = false): void
    {
        $this->teamRatingManager->updateRatings($homeTeam, $awayTeam, $homeScore, $awayScore, $distance, $homeRested, $awayRested, $neutralSite, $noFans, $isPlayoff, $homeQbChange, $awayQbChange);
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
            ->get();

        foreach ($pastSeasons as $game) {
            $homeStadium = $game->homeStadium;
            $awayStadium = $game->awayStadium;

            $distance = $this->distanceCalculator->calculateDistance($homeStadium, $awayStadium);

            $this->updateRatings(
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
        }
    }

    public function logExpectedWinningPercentageAndPredictedScore($game, $homeStadium, $awayStadium): void
    {
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
        $expectedPointsTable = [
            1 => 6.0, 2 => 5.8, 3 => 5.6, 4 => 5.4, 5 => 5.2,
            6 => 5.0, 7 => 4.8, 8 => 4.6, 9 => 4.4, 10 => 4.2,
            11 => 4.0, 12 => 3.8, 13 => 3.6, 14 => 3.4, 15 => 3.2,
            16 => 3.0, 17 => 2.8, 18 => 2.6, 19 => 2.4, 20 => 2.2,
            21 => 2.0, 22 => 1.8, 23 => 1.6, 24 => 1.4, 25 => 1.2,
            26 => 1.0, 27 => 0.8, 28 => 0.6, 29 => 0.4, 30 => 0.2,
            31 => 0.0, 32 => -0.2, 33 => -0.4, 34 => -0.6, 35 => -0.8,
            36 => -1.0, 37 => -1.2, 38 => -1.4, 39 => -1.6, 40 => -1.8,
            41 => -2.0, 42 => -2.2, 43 => -2.4, 44 => -2.6, 45 => -2.8,
            46 => -3.0, 47 => -3.2, 48 => -3.4, 49 => -3.6, 50 => -3.8,
            51 => -4.0, 52 => -4.2, 53 => -4.4, 54 => -4.6, 55 => -4.8,
            56 => -5.0, 57 => -5.2, 58 => -5.4, 59 => -5.6, 60 => -5.8,
            61 => -6.0, 62 => -6.2, 63 => -6.4, 64 => -6.6, 65 => -6.8,
            66 => -7.0, 67 => -7.2, 68 => -7.4, 69 => -7.6, 70 => -7.8,
            71 => -8.0, 72 => -8.2, 73 => -8.4, 74 => -8.6, 75 => -8.8,
            76 => -9.0, 77 => -9.2, 78 => -9.4, 79 => -9.6, 80 => -9.8,
            81 => -10.0, 82 => -10.2, 83 => -10.4, 84 => -10.6, 85 => -10.8,
            86 => -11.0, 87 => -11.2, 88 => -11.4, 89 => -11.6, 90 => -11.8,
            91 => -12.0, 92 => -12.2, 93 => -12.4, 94 => -12.6, 95 => -12.8,
            96 => -13.0, 97 => -13.2, 98 => -13.4, 99 => -13.6
        ];

        return $expectedPointsTable[$fieldPosition] ?? 0.0;
    }

    public function logEPARatings($homeTeamId, $homeEPA, $awayTeamId, $awayEPA): void
    {
        Log::info("EPA Ratings: Home Team ($homeTeamId): $homeEPA, Away Team ($awayTeamId): $awayEPA");
    }
}
