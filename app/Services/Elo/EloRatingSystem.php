<?php

namespace App\Services\Elo;

use App\Models\NflPrediction;
use App\Models\NflTeamSchedule;
use App\Models\NflPlayByPlay;
use App\Models\NflOdds;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class EloRatingSystem
{
    public EloCalculator $eloCalculator;
    public TeamRatingManager $teamRatingManager;
    private QBRatingManager $qbRatingManager;
    public DistanceCalculator $distanceCalculator;
    private DataStorage $dataStorage;
    private PredictionLoggingService $predictionLoggingService;
    private ExpectedWinsService $expectedWinsService;
    private ScorePredictionService $scorePredictionService;

    public function __construct()
    {
        $this->eloCalculator = new EloCalculator();
        $this->teamRatingManager = new TeamRatingManager();
        $this->qbRatingManager = new QBRatingManager();
        $this->distanceCalculator = new DistanceCalculator();
        $this->dataStorage = new DataStorage();
        $this->predictionLoggingService = new PredictionLoggingService($this->distanceCalculator, $this->eloCalculator, $this->teamRatingManager, $this);
        $this->expectedWinsService = new ExpectedWinsService($this->teamRatingManager, $this->distanceCalculator, $this->eloCalculator);
        $this->scorePredictionService = new ScorePredictionService($this->teamRatingManager, $this->eloCalculator);

        $this->initializeRatings();
    }

    private function initializeRatings(): void
    {
        $this->teamRatingManager->initializeRatings();
        $this->qbRatingManager->initializeQbRatings();
        $this->trainRatingsWithPastSeasons();
        $this->dataStorage->storeRatingsInDb($this->teamRatingManager->getRatings());
    }

    public function updateRatings(
        int   $homeTeam,
        int   $awayTeam,
        int   $homeScore,
        int   $awayScore,
        float $distance,
        bool  $homeRested = false,
        bool  $awayRested = false,
        bool  $neutralSite = false,
        bool  $noFans = false,
        bool  $isPlayoff = false,
        bool  $homeQbChange = false,
        bool  $awayQbChange = false,
        float $homeOdds,
        float $awayOdds
    ): array
    {
        $predictedHomePts = $this->calculatePredictedPoints($homeTeam, $awayTeam, $distance, $homeRested, $awayRested, $neutralSite, $noFans, $isPlayoff, $homeQbChange, $awayQbChange, $homeOdds);
        $predictedAwayPts = $this->calculatePredictedPoints($awayTeam, $homeTeam, $distance, $awayRested, $homeRested, $neutralSite, $noFans, $isPlayoff, $awayQbChange, $homeQbChange, -$awayOdds);

        $homeWinPercentage = $this->calculateWinningPercentage($homeTeam, $awayTeam, $homeOdds, $awayOdds);
        $awayWinPercentage = 100 - $homeWinPercentage;

        $this->teamRatingManager->updateRatings($homeTeam, $awayTeam, $homeScore, $awayScore, $distance, $homeRested, $awayRested, $neutralSite, $noFans, $isPlayoff, $homeQbChange, $awayQbChange);

        return [
            'home_pts' => $predictedHomePts,
            'away_pts' => $predictedAwayPts,
            'home_win_percentage' => $homeWinPercentage,
            'away_win_percentage' => $awayWinPercentage,
        ];
    }

    private function calculatePredictedPoints(
        int   $team1,
        int   $team2,
        float $distance,
        bool  $rested1,
        bool  $rested2,
        bool  $neutralSite,
        bool  $noFans,
        bool  $isPlayoff,
        bool  $qbChange1,
        bool  $qbChange2,
        float $odds
    ): float
    {
        $rating1 = $this->teamRatingManager->getRatings()[$team1];
        $rating2 = $this->teamRatingManager->getRatings()[$team2];

        $homeFieldAdjustment = $this->eloCalculator->calculateHomeFieldAdjustment($team1, $team2, $distance, $neutralSite, $noFans);
        $restAdjustment = ($rested1 ? $this->eloCalculator->restBonus : 0) - ($rested2 ? $this->eloCalculator->restBonus : 0);
        $qbAdjustment = ($qbChange1 ? -20 : 0) + ($qbChange2 ? 20 : 0);
        $playoffAdjustment = $isPlayoff ? $this->eloCalculator->playoffMultiplier : 1;
        $oddsAdjustment = $odds * $this->eloCalculator->oddsMultiplier;

        $adjustedRating1 = $rating1 + $homeFieldAdjustment + $restAdjustment + $qbAdjustment + $oddsAdjustment;
        $adjustedRating2 = $rating2 - $restAdjustment - $qbAdjustment - $oddsAdjustment;

        $pointSpread = ($adjustedRating1 - $adjustedRating2) * $playoffAdjustment;
        return $this->eloCalculator->averagePoints + ($pointSpread / 2);
    }

    private function calculateWinningPercentage(int $homeTeam, int $awayTeam, float $homeOdds, float $awayOdds): float
    {
        $rating1 = $this->teamRatingManager->getRatings()[$homeTeam];
        $rating2 = $this->teamRatingManager->getRatings()[$awayTeam];

        $oddsAdjustment = ($homeOdds - $awayOdds) * $this->eloCalculator->oddsMultiplier;
        $expectedScore1 = $this->eloCalculator->calculateExpectedScore($rating1 + $oddsAdjustment, $rating2 - $oddsAdjustment);

        return $expectedScore1 * 100;
    }

    public function trainRatingsWithPastSeasons(): void
    {
        $trainingService = new TrainingService($this->distanceCalculator, $this);
        $trainingService->trainRatingsWithPastSeasons();
    }

    public function logExpectedWinningPercentageAndPredictedScore(NflTeamSchedule $game, $homeStadium, $awayStadium): void
    {
        $this->predictionLoggingService->logExpectedWinningPercentageAndPredictedScore($game, $homeStadium, $awayStadium);
    }

    public function getActualScorePrediction(
        int   $teamA,
        int   $teamB,
        float $distance,
        bool  $neutralSite = false,
        bool  $noFans = false,
        bool  $isPlayoff = false,
        float $homeOdds = 0.0,
        float $awayOdds = 0.0
    ): array
    {
        return $this->scorePredictionService->getActualScorePrediction($teamA, $teamB, $distance, $neutralSite, $noFans, $isPlayoff, $homeOdds, $awayOdds);
    }

    public function getRatings(): array
    {
        return $this->teamRatingManager->getRatings();
    }

    public function calculateExpectedWinsForTeams(): array
    {
        return $this->expectedWinsService->calculateExpectedWinsForTeams();
    }

    public function updateEPARatingsAfterGame(int $gameId): void
    {
        $game = NflTeamSchedule::find($gameId);

        $homeEPA = $this->calculateTotalEPAForTeam($game->team_id_home, $gameId);
        $awayEPA = $this->calculateTotalEPAForTeam($game->team_id_away, $gameId);

        $this->logEPARatings($game->team_id_home, $homeEPA, $game->team_id_away, $awayEPA);
    }

    public function calculateTotalEPAForTeam(int $teamId, int $gameId): float
    {
        $plays = $this->getPlaysForTeamInGame($teamId, $gameId);

        return array_reduce($plays, fn($totalEPA, $play) => $totalEPA + $this->calculateEPA($play), 0);
    }

    public function getPlaysForTeamInGame(int $teamId, int $gameId): array
    {
        return NflPlayByPlay::where('team_id', $teamId)
            ->where('game_id', $gameId)
            ->pluck('play')
            ->toArray();
    }

    public function calculateEPA(array $playData): float
    {
        $startFieldPosition = $this->parseFieldPositionFromPlay($playData['play']);
        $endFieldPosition = $this->parseFieldPositionFromPlay($playData['play']);

        $expectedPointsBefore = $this->getExpectedPoints($startFieldPosition);
        $expectedPointsAfter = $this->getExpectedPoints($endFieldPosition);

        return $expectedPointsAfter - $expectedPointsBefore;
    }

    public function parseFieldPositionFromPlay(string $playDescription): int
    {
        if (preg_match('/to\s[A-Z]{2,3}\s(\d{1,2})/', $playDescription, $matches)) {
            return (int)$matches[1];
        }

        return 0; // Fallback if extraction fails
    }

    public function parseFieldPositionFromDownAndDistance(string $downAndDistance): int
    {
        if (preg_match('/at\s[A-Z]{2,3}\s(\d{1,2})/', $downAndDistance, $matches)) {
            return (int)$matches[1];
        }

        return 0; // Fallback if extraction fails
    }

    public function getExpectedPoints(int $fieldPosition): float
    {
        $expectedPointsTable = Config::get('nfl.expectedPointsTable');
        return $expectedPointsTable[$fieldPosition] ?? 0.0;
    }

    public function logEPARatings(int $homeTeamId, float $homeEPA, int $awayTeamId, float $awayEPA): void
    {
        Log::info("EPA Ratings: Home Team ($homeTeamId): $homeEPA, Away Team ($awayTeamId): $awayEPA");
    }

    public function calculateExpectedWins($teamId): float
    {
        $expectedWins = $this->calculateExpectedWinsForTeams();
        return $expectedWins[$teamId] ?? 0.0;
    }
}
