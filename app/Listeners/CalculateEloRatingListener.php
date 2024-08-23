<?php

namespace App\Listeners;

use App\Events\CalculateEloRating;
use App\Events\CalculateStadiumDistance;
use App\Models\NflOdds;
use App\Models\NflTeamSchedule;
use App\Models\EspnNflDepthChart;
use App\Models\NflEspnInjury;
use App\Models\NflEspnTeamStat;
use Illuminate\Support\Facades\Log;

class CalculateEloRatingListener
{
    protected int $kFactor = 20;
    protected array $initialEloRatings = [];
    protected int $homeFieldAdvantage = 55; // Example value for home-field advantage

    // Define weights for each category
    protected array $categoryWeights = [
        'passing' => 0.05,
        'scoring' => 0.1,
        'defensive' => 0.05,
    ];

    /**
     * Handle the event.
     *
     * @param CalculateEloRating $event
     * @return void
     */
    public function handle(CalculateEloRating $event)
    {
        $teamId = $event->teamId;
        $seasonType = $event->seasonType;

        // Calculate the Elo rating
        $initialElo = $this->getInitialElo($teamId, $seasonType);

        Log::info('Elo Rating calculated for team ' . $teamId . ': ' . $initialElo);
    }

    /**
     * Fetch initial Elo rating for a team, incorporating MOV, distance, and category-based adjustments.
     */
    protected function getInitialElo($teamId, $seasonType = 'Regular Season')
    {
        if (isset($this->initialEloRatings[$teamId])) {
            Log::info('Returning existing Elo for team ' . $teamId . ': ' . $this->initialEloRatings[$teamId]);
            return $this->initialEloRatings[$teamId];
        }

        // Initialize the base Elo rating
        $initialElo = 1500;

        // Retrieve all odds involving this team
        $teamOdds = NflOdds::where('home_team_id', $teamId)->orWhere('away_team_id', $teamId)->get();

        if ($teamOdds->isEmpty()) {
            Log::warning('No odds data found for team ' . $teamId . '. Returning default Elo.');
            return $initialElo;
        }

        $gamesCount = 0;
        foreach ($teamOdds as $odds) {
            $isHomeTeam = $odds->home_team_id == $teamId;
            $impliedProbability = $isHomeTeam
                ? $this->convertMoneylineToProbability($odds->h2h_home_price)
                : $this->convertMoneylineToProbability($odds->h2h_away_price);

            $adjustment = ($impliedProbability - 0.5) * $this->kFactor;

            // Incorporate MOV into the Elo adjustment with season type filtering
            $teamSchedules = NflTeamSchedule::where(function ($query) use ($teamId) {
                $query->where('team_id_home', $teamId)
                    ->orWhere('team_id_away', $teamId);
            })
                ->where('season_type', $seasonType)
                ->where(function ($query) {
                    $currentYear = now()->year;
                    $previousYear = $currentYear - 1;
                    $query->whereYear('game_date', $currentYear)
                        ->orWhereYear('game_date', $previousYear);
                })
                ->limit(10)
                ->get();

            foreach ($teamSchedules as $teamSchedule) {
                $mov = $teamSchedule->getMarginOfVictory();
                $winner = $teamSchedule->getWinner();

                $movAdjustment = log(max(1, $mov + 1)) * ($winner === 'home' ? 1 : -1);
                $adjustment += $movAdjustment;
                $gamesCount++;
            }

            if ($gamesCount > 0) {
                $initialElo += $adjustment / $gamesCount; // Average adjustment
            }

            // Apply distance-based adjustment using the CalculateStadiumDistance event
            $distance = event(new CalculateStadiumDistance($odds->home_team_id, $odds->away_team_id))[0];
            if ($distance !== null) {
                $distanceAdjustment = $distance / 100; // Adjust the divisor to scale the impact
                $initialElo -= $distanceAdjustment;

                Log::info("Distance Adjustment for Team $teamId: $distanceAdjustment (Distance: $distance km)");
            }

            // Apply rank adjustments based on categories
            $initialElo = $this->applyCategoryRankAdjustments($initialElo, $teamId);
        }

        Log::info('Final Initial Elo for team ' . $teamId . ': ' . $initialElo);
        $this->initialEloRatings[$teamId] = $initialElo;

        return $initialElo;
    }

    /**
     * Apply rank adjustments based on categories, only if stats are available.
     */
    protected function applyCategoryRankAdjustments($initialElo, $teamId): float|int
    {
        $currentSeason = now()->year;

        foreach ($this->categoryWeights as $category => $weight) {
            $averageRank = (new NflEspnTeamStat)->calculateAverageRank($teamId, $currentSeason, $category);

            if ($averageRank !== null) {
                $normalizedRank = $averageRank / 32; // Assuming 32 teams in the league
                $rankAdjustment = $normalizedRank * $weight * ($initialElo / 50);

                if ($averageRank > 16) {
                    $rankAdjustment *= 1.2; // Steeper penalty for worse ranks
                } else {
                    $rankAdjustment *= 0.8; // Milder bonus for better ranks
                }

                $initialElo -= $rankAdjustment;

                Log::info("Rank Adjustment for Team $teamId in category '$category': $rankAdjustment");
            } else {
                // If no stats are found, apply a base adjustment
                $baseAdjustment = $weight * ($initialElo / 100); // Example base adjustment logic
                $initialElo -= $baseAdjustment;

                Log::info("No stats found for Team $teamId in category '$category', applying base adjustment: $baseAdjustment");
            }
        }
        return $initialElo;
    }

    /**
     * Adjust Elo rating based on team injuries.
     */
    protected function adjustEloForInjuries($elo, $teamId): float|int
    {
        // Get injured players grouped by position
        $injuryCounts = $this->getInjuredPlayersCountByPosition($teamId);

        // Factors that can influence the impact of an injury on Elo
        $positionImpact = [
            'Quarterback' => 0.5,   // Quarterbacks have a higher impact
            'Running Back' => 0.5,
            'Wide Receiver' => 0.15,
            'Tight End' => 0.1,   // Tight End
            'Offensive Tackle' => 0.1,    // Offensive line
            'Guard' => 0.1,    // Offensive line
            'Center' => 0.1,    // Offensive line
            'Defensive End' => 0.5,    // Defensive line
            'Defensive Tackle' => 0.5,    // Defensive line
            'Linebacker' => 0.5,    // Linebacker
            'Cornerback' => 0.5,    // Defensive back
            'Safety' => 0.5,    // Defensive back
            'Long Snapper' => 0.5,    // Long snapper
            'Place kicker' => 0.5,    // Kicker
            'Punter' => 0.5,    // Punter
        ];

        // Aggregate the impact of injuries by position
        foreach ($injuryCounts as $position => $injuryCount) {
            $positionEloImpact = $positionImpact[$position] ?? 0.5; // Default impact if position not listed

            // Calculate the total impact on the Elo rating
            $totalImpact = $positionEloImpact * ($injuryCount / 32); // Normalize by dividing by a factor

            $elo -= $totalImpact;
        }

        return $elo;
    }

    /**
     * Get the count of injured players grouped by position.
     */
    protected function getInjuredPlayersCountByPosition($teamId): array
    {
        // Get the depth chart for the specified team
        $depthChart = EspnNflDepthChart::where('team_id', $teamId)->get();

        // Get all injured players for the specified team
        $injuredPlayers = NflEspnInjury::where('team_id', $teamId)
            ->where('status', '!=', 'Active') // Assuming 'Healthy' is a status indicating no injury
            ->get();

        // Group injured players by position
        $injuryCounts = [];

        $depthChart->each(function ($player) use ($injuredPlayers, &$injuryCounts) {
            if ($injuredPlayers->contains('athlete_id', $player->athlete_id)) {
                $position = $player->position;
                if (!isset($injuryCounts[$position])) {
                    $injuryCounts[$position] = 0;
                }
                $injuryCounts[$position]++;
            }
        });

        return $injuryCounts;
    }

    /**
     * Convert moneyline odds to implied probability.
     */
    protected function convertMoneylineToProbability($moneyline)
    {
        $moneyline = (float)$moneyline;
        return $moneyline > 0 ? 100 / ($moneyline + 100) : abs($moneyline) / (abs($moneyline) + 100);
    }
}
