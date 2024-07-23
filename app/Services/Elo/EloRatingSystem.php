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
        float $awayOdds,
    ): array
    {
        $predictedHomePts = $this->calculatePredictedPoints($homeTeam, $awayTeam, $homeScore, $awayScore, $distance, $homeRested, $awayRested, $neutralSite, $noFans, $isPlayoff, $homeQbChange, $awayQbChange, $homeOdds);
        $predictedAwayPts = $this->calculatePredictedPoints($awayTeam, $homeTeam, $awayScore, $homeScore, $distance, $awayRested, $homeRested, $neutralSite, $noFans, $isPlayoff, $awayQbChange, $homeQbChange, -$awayOdds);

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
        int   $score1,
        int   $score2,
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
        $oddsAdjustment = $odds * $this->eloCalculator->oddsMultiplier; // Use multiplier from config

        $adjustedRating1 = $rating1 + $homeFieldAdjustment + $restAdjustment + $qbAdjustment + $oddsAdjustment;
        $adjustedRating2 = $rating2 - $restAdjustment - $qbAdjustment - $oddsAdjustment;

        $pointSpread = ($adjustedRating1 - $adjustedRating2) * $playoffAdjustment;

        $averagePoints = $this->eloCalculator->averagePoints;
        $predictedPoints = $averagePoints + ($pointSpread / 2);

        return $predictedPoints;
    }

    private function calculateWinningPercentage(int $homeTeam, int $awayTeam, float $homeOdds, float $awayOdds): float
    {
        $rating1 = $this->teamRatingManager->getRatings()[$homeTeam];
        $rating2 = $this->teamRatingManager->getRatings()[$awayTeam];

        $oddsAdjustment = ($homeOdds - $awayOdds) * $this->eloCalculator->oddsMultiplier; // Use multiplier from config
        $expectedScore1 = $this->eloCalculator->calculateExpectedScore($rating1 + $oddsAdjustment, $rating2 - $oddsAdjustment);

        return $expectedScore1 * 100;
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

            $predictedRatings = $this->updateRatings(
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

            if ($predictedRatings === null) {
                Log::error('Predicted ratings are null for game ID: ' . $game->game_id);
                continue;
            }

            if (!isset($predictedRatings['home_pts']) || !isset($predictedRatings['away_pts'])) {
                Log::error('Missing keys in predicted ratings for game ID: ' . $game->game_id);
                continue;
            }

            // $this->logGamePredictions($game, $predictedRatings);
        }
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
            $homeOdds = $odds ? (float)$odds->h2h_home_price : 0.0;
            $awayOdds = $odds ? (float)$odds->h2h_away_price : 0.0;

            $prediction = $this->getActualScorePrediction(
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

    public function getActualScorePrediction(int $teamA, int $teamB, float $distance, bool $neutralSite = false, bool $noFans = false, bool $isPlayoff = false, float $homeOdds = 0.0, float $awayOdds = 0.0): array
    {
        $eloDiff = $this->teamRatingManager->getRatings()[$teamA] - $this->teamRatingManager->getRatings()[$teamB] + $this->eloCalculator->calculateHomeFieldAdjustment($teamA, $teamB, $distance, $neutralSite, $noFans);
        if ($isPlayoff) {
            $eloDiff *= $this->eloCalculator->playoffMultiplier;
        }
        $oddsAdjustment = ($homeOdds - $awayOdds) * $this->eloCalculator->oddsMultiplier; // Use multiplier from config
        $pointSpread = $this->eloCalculator->calculateExpectedPointSpread($eloDiff + $oddsAdjustment);
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

        $teamRatings = $this->teamRatingManager->getRatings();
        Log::info('Team Ratings:', $teamRatings);

        $expectedWins = array_fill_keys(array_keys($teamRatings), 0);

        $batchSize = 100; // Define batch size
        $batches = $futureGames->chunk($batchSize);

        foreach ($batches as $batch) {
            foreach ($batch as $game) {
                $distance = $this->distanceCalculator->calculateDistance($game->homeStadium, $game->awayStadium);
                Log::info('Game:', ['game_id' => $game->id, 'home_team' => $game->team_id_home, 'away_team' => $game->team_id_away, 'distance' => $distance]);

                $homeTeamRating = $teamRatings[$game->team_id_home] ?? 0;
                $awayTeamRating = $teamRatings[$game->team_id_away] ?? 0;

                Log::info('Ratings:', ['home_team' => $game->team_id_home, 'rating' => $homeTeamRating, 'away_team' => $game->team_id_away, 'rating' => $awayTeamRating]);

                $homeWinRecord = $this->eloCalculator->getCurrentSeasonWinningRecord($game->team_id_home);
                $awayWinRecord = $this->eloCalculator->getCurrentSeasonWinningRecord($game->team_id_away);

                $homeRatingWithRecord = $homeTeamRating * (1 + $homeWinRecord);
                $awayRatingWithRecord = $awayTeamRating * (1 + $awayWinRecord);

                $expectedHomeWinProbability = $this->eloCalculator->calculateExpectedScore(
                    $homeRatingWithRecord,
                    $awayRatingWithRecord,
                    false
                );

                Log::info('Expected Win Probability:', ['home_team' => $game->team_id_home, 'probability' => $expectedHomeWinProbability]);

                $expectedAwayWinProbability = 1 - $expectedHomeWinProbability;

                $expectedWins[$game->team_id_home] += $expectedHomeWinProbability;
                $expectedWins[$game->team_id_away] += $expectedAwayWinProbability;

                Log::info('Accumulated Expected Wins:', ['home_team' => $game->team_id_home, 'expected_wins' => $expectedWins[$game->team_id_home], 'away_team' => $game->team_id_away, 'expected_wins' => $expectedWins[$game->team_id_away]]);
            }
        }

        Log::info('Final Expected Wins:', $expectedWins);

        return $expectedWins;
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
