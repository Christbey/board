<?php

namespace App\Services;

use App\Models\EloRating;
use App\Models\NflPlayByPlay;
use App\Models\NflTeamSchedule;
use App\Models\NFLStadium;
use Illuminate\Support\Facades\DB;
use Log;

/*class EloRatingSystem
{
    private array $ratings;
    private array $qbRatings;
    private mixed $homeFieldAdvantage;
    private mixed $kFactor;
    private mixed $distanceFactor;
    private mixed $restBonus;
    private mixed $playoffMultiplier;
    private mixed $averagePoints;

    public function __construct($initialRating = 1500, $homeFieldAdvantage = 48, $kFactor = 20, $distanceFactor = 4, $restBonus = 25, $playoffMultiplier = 1.2, $averagePoints = 21)
    {
        $teams = $this->getAllTeams();
        $this->ratings = array_fill_keys($teams, $initialRating);
        $this->qbRatings = array_fill_keys($teams, 0);
        $this->homeFieldAdvantage = $homeFieldAdvantage;
        $this->kFactor = $kFactor;
        $this->distanceFactor = $distanceFactor;
        $this->restBonus = $restBonus;
        $this->playoffMultiplier = $playoffMultiplier;
        $this->averagePoints = $averagePoints;

        $this->trainRatingsWithPastSeasons();
        $this->storeRatingsInDb();
    }

    private function getAllTeams(): array
    {
        return DB::table('nfl_teams')->pluck('id')->toArray();
    }

    public function calculateExpectedScore($ratingA, $ratingB, $isPlayoff = false): float|int
    {
        $eloDiff = $ratingA - $ratingB;
        if ($isPlayoff) {
            $eloDiff *= $this->playoffMultiplier;
        }
        return 1 / (1 + pow(10, -$eloDiff / 400));
    }

    private function calculateHomeFieldAdjustment($homeTeam, $awayTeam, $distance, $neutralSite = false, $noFans = false)
    {
        if ($neutralSite) {
            return $this->distanceFactor * ($distance / 1000);
        }

        $baseAdvantage = $noFans ? 33 : $this->homeFieldAdvantage;
        return $baseAdvantage + $this->distanceFactor * ($distance / 1000);
    }

    private function calculateMarginOfVictoryMultiplier($winnerScore, $loserScore, $eloDiff): float
    {
        $pointDiff = $winnerScore - $loserScore;
        return log($pointDiff + 1) * (2.2 / (($eloDiff * 0.001) + 2.2));
    }

    public function updateRatings($homeTeam, $awayTeam, $homeScore, $awayScore, $distance, $homeRested = false, $awayRested = false, $neutralSite = false, $noFans = false, $isPlayoff = false, $homeQbChange = false, $awayQbChange = false): void
    {
        $homeFieldAdjustment = $this->calculateHomeFieldAdjustment($homeTeam, $awayTeam, $distance, $neutralSite, $noFans);
        $homeRating = $this->ratings[$homeTeam] + $homeFieldAdjustment;
        $awayRating = $this->ratings[$awayTeam];

        $homeQBRating = $this->qbRatings[$homeTeam];
        $awayQBRating = $this->qbRatings[$awayTeam];

        if ($homeRested) {
            $homeRating += $this->restBonus;
        }
        if ($awayRested) {
            $awayRating += $this->restBonus;
        }

        if ($homeQbChange) {
            $homeRating += $homeQBRating;
        }
        if ($awayQbChange) {
            $awayRating += $awayQBRating;
        }

        $expectedHomeScore = $this->calculateExpectedScore($homeRating, $awayRating, $isPlayoff);
        $expectedAwayScore = 1 - $expectedHomeScore;

        // Use actual scores if available, otherwise use expected scores
        $actualHomeScore = $homeScore !== null ? ($homeScore > $awayScore ? 1 : ($homeScore < $awayScore ? 0 : 0.5)) : $expectedHomeScore;
        $actualAwayScore = $homeScore !== null ? 1 - $actualHomeScore : $expectedAwayScore;

        $winnerScore = max($homeScore, $awayScore);
        $loserScore = min($homeScore, $awayScore);
        $eloDiff = abs($homeRating - $awayRating);
        $marginOfVictoryMultiplier = $this->calculateMarginOfVictoryMultiplier($winnerScore, $loserScore, $eloDiff);

        $kFactorAdjusted = $this->kFactor * $marginOfVictoryMultiplier;

        $this->ratings[$homeTeam] += $kFactorAdjusted * ($actualHomeScore - $expectedHomeScore);
        $this->ratings[$awayTeam] += $kFactorAdjusted * ($actualAwayScore - $expectedAwayScore);
    }

    public function getRatings()
    {
        return $this->ratings;
    }

    public function setQbRating($team, $rating): void
    {
        $this->qbRatings[$team] = $rating;
    }

    public function calculateWinProbability($teamA, $teamB, $distance, $neutralSite = false, $noFans = false, $isPlayoff = false): float|int
    {
        $eloDiff = $this->ratings[$teamA] - $this->ratings[$teamB] + $this->calculateHomeFieldAdjustment($teamA, $teamB, $distance, $neutralSite, $noFans);
        if ($isPlayoff) {
            $eloDiff *= $this->playoffMultiplier;
        }
        return 1 / (1 + pow(10, -$eloDiff / 400));
    }

    public function calculateQbValue($stats): float
    {
        return -2.2 * $stats['pass_attempts'] +
            3.7 * $stats['completions'] +
            ($stats['passing_yards'] / 5) +
            11.3 * $stats['passing_tds'] -
            14.1 * $stats['interceptions'] -
            8 * $stats['sacks'] -
            1.1 * $stats['rush_attempts'] +
            0.6 * $stats['rushing_yards'] +
            15.9 * $stats['rushing_tds'];
    }

    public function updateQbRating($team, $qbStats, $opponentDefense): void
    {
        $defenseAdjustment = $opponentDefense - $this->getLeagueAverageValue();
        $gameValue = $this->calculateQbValue($qbStats) - $defenseAdjustment;
        $this->qbRatings[$team] = 0.9 * $this->qbRatings[$team] + 0.1 * $gameValue;
    }

    public function getLeagueAverageValue()
    {
        return DB::table('nfl_player_stats')->avg(DB::raw('
            -2.2 * pass_attempts +
            3.7 * completions +
            (passing_yards / 5) +
            11.3 * passing_tds -
            14.1 * interceptions -
            8 * sacks -
            1.1 * rush_attempts +
            0.6 * rushing_yards +
            15.9 * rushing_tds
        '));
    }

    public function calculateExpectedPointSpread($eloDiff): float|int
    {
        return $eloDiff / 25; // A typical Elo point spread conversion factor
    }

    public function getActualScorePrediction($teamA, $teamB, $distance, $neutralSite = false, $noFans = false, $isPlayoff = false): array
    {
        $eloDiff = $this->ratings[$teamA] - $this->ratings[$teamB] + $this->calculateHomeFieldAdjustment($teamA, $teamB, $distance, $neutralSite, $noFans);
        if ($isPlayoff) {
            $eloDiff *= $this->playoffMultiplier;
        }
        $pointSpread = $this->calculateExpectedPointSpread($eloDiff);
        $averagePoints = $this->averagePoints;

        $expectedScoreA = $averagePoints + ($pointSpread / 2);
        $expectedScoreB = $averagePoints - ($pointSpread / 2);

        return [
            'teamA' => round($expectedScoreA),
            'teamB' => round($expectedScoreB)
        ];
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
            $homeStadium = NFLStadium::find($game->team_id_home);
            $awayStadium = NFLStadium::find($game->team_id_away);

            $distance = $this->calculateDistance($homeStadium, $awayStadium);

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

    protected function storeRatingsInDb(): void
    {
        foreach ($this->ratings as $teamId => $rating) {
            EloRating::updateOrCreate(
                ['team_id' => $teamId],
                ['rating' => $rating]
            );
        }
    }

    public function logExpectedWinningPercentageAndPredictedScore($game, $homeStadium, $awayStadium): void
    {
        $distance = $this->calculateDistance($homeStadium, $awayStadium);

        $expectedHomeScore = $this->calculateExpectedScore(
            $this->ratings[$game->team_id_home],
            $this->ratings[$game->team_id_away]
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

    private function calculateDistance($homeStadium, $awayStadium): float|int
    {
        if ($homeStadium && $awayStadium) {
            return $this->haversineGreatCircleDistance(
                $homeStadium->latitude,
                $homeStadium->longitude,
                $awayStadium->latitude,
                $awayStadium->longitude
            );
        }
        return 0; // Fallback if stadium data is missing
    }

    private function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371): float|int
    {
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }

    public function calculateExpectedWinsForTeams(): array
    {
        $futureGames = NflTeamSchedule::whereNull('home_result')
            ->whereNull('away_result')
            ->where('game_status', 'scheduled')
            ->get();

        $expectedWins = array_fill_keys(array_keys($this->ratings), 0);

        foreach ($futureGames as $game) {
            $homeStadium = NFLStadium::find($game->team_id_home);
            $awayStadium = NFLStadium::find($game->team_id_away);

            $distance = $this->calculateDistance($homeStadium, $awayStadium);

            $expectedHomeWinProbability = $this->calculateWinProbability(
                $game->team_id_home,
                $game->team_id_away,
                $distance,
                false, // Example neutral site flag, replace with actual logic
                false, // Example no fans flag, replace with actual logic
                $game->season_type === 'playoff'
            );

            $expectedAwayWinProbability = 1 - $expectedHomeWinProbability;

            $expectedWins[$game->team_id_home] += $expectedHomeWinProbability;
            $expectedWins[$game->team_id_away] += $expectedAwayWinProbability;
        }

        return $expectedWins;
    }

    // Method to calculate EPA based on play data
    public function calculateEPA($playData): float
    {
        $startFieldPosition = $this->parseFieldPositionFromPlay($playData['play']);
        $endFieldPosition = $this->parseFieldPositionFromPlay($playData['play']);

        $expectedPointsBefore = $this->getExpectedPoints($startFieldPosition);
        $expectedPointsAfter = $this->getExpectedPoints($endFieldPosition);

        return $expectedPointsAfter - $expectedPointsBefore;
    }

    // Method to parse field position from play description
    public function parseFieldPositionFromDownAndDistance($downAndDistance): int|string
    {
        // Example format: '1st & 10 at BUF 25'
        if (preg_match('/at\s[A-Z]{2,3}\s(\d{1,2})/', $downAndDistance, $matches)) {
            return $matches[1]; // Return the yard line
        }

        return 0; // Fallback if extraction fails
    }

    public function parseFieldPositionFromPlay($playDescription): int|string
    {
        // Logic to parse field position from play description
        // Example regex or string parsing logic
        if (preg_match('/to\s[A-Z]{2,3}\s(\d{1,2})/', $playDescription, $matches)) {
            return $matches[1]; // Return the yard line
        }

        return 0; // Fallback if extraction fails
    }

    public function getExpectedPoints($fieldPosition): float
    {
        // Define a comprehensive example expected points table
        $expectedPointsTable = [
            '1' => 6.0, // 1 yard line
            '2' => 5.8, // 2 yard line
            '3' => 5.6, // 3 yard line
            '4' => 5.4, // 4 yard line
            '5' => 5.2, // 5 yard line
            '6' => 5.0, // 6 yard line
            '7' => 4.8, // 7 yard line
            '8' => 4.6, // 8 yard line
            '9' => 4.4, // 9 yard line
            '10' => 4.2, // 10 yard line
            '11' => 4.0, // 11 yard line
            '12' => 3.8, // 12 yard line
            '13' => 3.6, // 13 yard line
            '14' => 3.4, // 14 yard line
            '15' => 3.2, // 15 yard line
            '16' => 3.0, // 16 yard line
            '17' => 2.8, // 17 yard line
            '18' => 2.6, // 18 yard line
            '19' => 2.4, // 19 yard line
            '20' => 2.2, // 20 yard line
            '21' => 2.0, // 21 yard line
            '22' => 1.8, // 22 yard line
            '23' => 1.6, // 23 yard line
            '24' => 1.4, // 24 yard line
            '25' => 1.2, // 25 yard line
            '26' => 1.0, // 26 yard line
            '27' => 0.8, // 27 yard line
            '28' => 0.6, // 28 yard line
            '29' => 0.4, // 29 yard line
            '30' => 0.2, // 30 yard line
            '31' => 0.0, // 31 yard line
            '32' => -0.2, // 32 yard line
            '33' => -0.4, // 33 yard line
            '34' => -0.6, // 34 yard line
            '35' => -0.8, // 35 yard line
            '36' => -1.0, // 36 yard line
            '37' => -1.2, // 37 yard line
            '38' => -1.4, // 38 yard line
            '39' => -1.6, // 39 yard line
            '40' => -1.8, // 40 yard line
            '41' => -2.0, // 41 yard line
            '42' => -2.2, // 42 yard line
            '43' => -2.4, // 43 yard line
            '44' => -2.6, // 44 yard line
            '45' => -2.8, // 45 yard line
            '46' => -3.0, // 46 yard line
            '47' => -3.2, // 47 yard line
            '48' => -3.4, // 48 yard line
            '49' => -3.6, // 49 yard line
            '50' => 2.5, // 50 yard line
            '51' => 2.7, // Opponent's 49 yard line
            '52' => 2.9, // Opponent's 48 yard line
            '53' => 3.1, // Opponent's 47 yard line
            '54' => 3.3, // Opponent's 46 yard line
            '55' => 3.5, // Opponent's 45 yard line
            '56' => 3.7, // Opponent's 44 yard line
            '57' => 3.9, // Opponent's 43 yard line
            '58' => 4.1, // Opponent's 42 yard line
            '59' => 4.3, // Opponent's 41 yard line
            '60' => 4.5, // Opponent's 40 yard line
            '61' => 4.7, // Opponent's 39 yard line
            '62' => 4.9, // Opponent's 38 yard line
            '63' => 5.1, // Opponent's 37 yard line
            '64' => 5.3, // Opponent's 36 yard line
            '65' => 5.5, // Opponent's 35 yard line
            '66' => 5.7, // Opponent's 34 yard line
            '67' => 5.9, // Opponent's 33 yard line
            '68' => 6.1, // Opponent's 32 yard line
            '69' => 6.3, // Opponent's 31 yard line
            '70' => 6.5, // Opponent's 30 yard line
            '71' => 6.7, // Opponent's 29 yard line
            '72' => 6.9, // Opponent's 28 yard line
            '73' => 7.1, // Opponent's 27 yard line
            '74' => 7.3, // Opponent's 26 yard line
            '75' => 7.5, // Opponent's 25 yard line
            '76' => 7.7, // Opponent's 24 yard line
            '77' => 7.9, // Opponent's 23 yard line
            '78' => 8.1, // Opponent's 22 yard line
            '79' => 8.3, // Opponent's 21 yard line
            '80' => 8.5, // Opponent's 20 yard line
            '81' => 8.7, // Opponent's 19 yard line
            '82' => 8.9, // Opponent's 18 yard line
            '83' => 9.1, // Opponent's 17 yard line
            '84' => 9.3, // Opponent's 16 yard line
            '85' => 9.5, // Opponent's 15 yard line
            '86' => 9.7, // Opponent's 14 yard line
            '87' => 9.9, // Opponent's 13 yard line
            '88' => 10.1, // Opponent's 12 yard line
            '89' => 10.3, // Opponent's 11 yard line
            '90' => 10.5, // Opponent's 10 yard line
            '91' => 10.7, // Opponent's 9 yard line
            '92' => 10.9, // Opponent's 8 yard line
            '93' => 11.1, // Opponent's 7 yard line
            '94' => 11.3, // Opponent's 6 yard line
            '95' => 11.5, // Opponent's 5 yard line
            '96' => 11.7, // Opponent's 4 yard line
            '97' => 11.9, // Opponent's 3 yard line
            '98' => 12.1, // Opponent's 2 yard line
            '99' => 12.3, // Opponent's 1 yard line
        ];

        return $expectedPointsTable[$fieldPosition] ?? 0.0;
    }

    // Method to calculate total EPA for a team in a game
    public function calculateTotalEPAForTeam($teamId, $gameId): float|int
    {
        $plays = $this->getPlaysForTeamInGame($teamId, $gameId);

        $totalEPA = 0;
        foreach ($plays as $play) {
            $totalEPA += $this->calculateEPA($play);
        }

        return $totalEPA;
    }

    // Placeholder method to fetch plays for a team in a game
    protected function getPlaysForTeamInGame($teamId, $gameId): array
    {
        return NflPlayByPlay::where('team_id', $teamId)
            ->where('game_id', $gameId)
            ->select('play')
            ->get()
            ->toArray();
    }

    // Method to update EPA ratings for all teams after a game
    public function updateEPARatingsAfterGame($gameId): void
    {
        $game = NflTeamSchedule::find($gameId);

        $homeEPA = $this->calculateTotalEPAForTeam($game->team_id_home, $gameId);
        $awayEPA = $this->calculateTotalEPAForTeam($game->team_id_away, $gameId);

        $this->logEPARatings($game->team_id_home, $homeEPA, $game->team_id_away, $awayEPA);
    }

    // Placeholder method to log EPA ratings
    protected function logEPARatings($homeTeamId, $homeEPA, $awayTeamId, $awayEPA): void
    {
        Log::info("EPA Ratings: Home Team ($homeTeamId): $homeEPA, Away Team ($awayTeamId): $awayEPA");
    }

    // ... other existing methods ...
}*/
