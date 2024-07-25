<?php

namespace App\Services\Elo;

use App\Models\NflTeamSchedule;
use App\Models\NflStadium;
use App\Models\NflOdds;
use Illuminate\Support\Facades\Log;

class ExpectedWinsService
{
    private TeamRatingManager $teamRatingManager;
    private DistanceCalculator $distanceCalculator;
    private EloCalculator $eloCalculator;

    public function __construct(TeamRatingManager $teamRatingManager, DistanceCalculator $distanceCalculator, EloCalculator $eloCalculator)
    {
        $this->teamRatingManager = $teamRatingManager;
        $this->distanceCalculator = $distanceCalculator;
        $this->eloCalculator = $eloCalculator;
    }

    public function calculateExpectedWinsForTeams(): array
    {
        $futureGames = NflTeamSchedule::where(function ($query) {
            $query->whereNull('home_result')
                ->orWhere('home_result', '');
        })
            ->where(function ($query) {
                $query->whereNull('away_result')
                    ->orWhere('away_result', '');
            })
            ->where('game_status', 'scheduled')
            ->get();

        Log::info('Fetched Future Games Count', ['count' => $futureGames->count()]);
        Log::info('Fetched Future Games', ['futureGames' => $futureGames]);

        $expectedWins = array_fill_keys(array_keys($this->teamRatingManager->getRatings()), 0);

        // Fetch all stadiums to minimize queries
        $stadiums = NflStadium::whereIn('team_id', $futureGames->pluck('team_id_home')->merge($futureGames->pluck('team_id_away'))->unique())->get()->keyBy('team_id');
        Log::info('Fetched Stadiums', ['stadiums' => $stadiums]);

        foreach ($futureGames as $game) {
            Log::info('Processing Game', ['game_id' => $game->game_id, 'team_id_home' => $game->team_id_home, 'team_id_away' => $game->team_id_away]);

            $homeStadium = $stadiums->get($game->team_id_home);
            $awayStadium = $stadiums->get($game->team_id_away);

            if (!$homeStadium || !$awayStadium) {
                Log::error('Missing stadium information for game', [
                    'game_id' => $game->game_id,
                    'home_stadium' => $homeStadium,
                    'away_stadium' => $awayStadium,
                ]);
                continue;
            }

            $distance = $this->distanceCalculator->calculateDistance($homeStadium, $awayStadium);
            Log::info('Calculated Distance', ['distance' => $distance]);

            $odds = NflOdds::where('home_team_id', $game->team_id_home)
                ->where('away_team_id', $game->team_id_away)
                ->first();

            if (!$odds) {
                Log::error('Missing odds information for game', [
                    'game_id' => $game->game_id,
                    'home_team_id' => $game->team_id_home,
                    'away_team_id' => $game->team_id_away,
                ]);
                continue;
            }

            $homeOdds = $odds->h2h_home_price ?? 0.0;
            $awayOdds = $odds->h2h_away_price ?? 0.0;
            Log::info('Fetched Odds', ['homeOdds' => $homeOdds, 'awayOdds' => $awayOdds]);

            $homeTeamRating = $this->teamRatingManager->getRatings()[$game->team_id_home] ?? null;
            $awayTeamRating = $this->teamRatingManager->getRatings()[$game->team_id_away] ?? null;

            if ($homeTeamRating === null || $awayTeamRating === null) {
                Log::error('Missing team rating for game', [
                    'game_id' => $game->game_id,
                    'team_id_home' => $game->team_id_home,
                    'team_id_away' => $game->team_id_away,
                ]);
                continue;
            }

            $homeWinRecord = $this->eloCalculator->getCurrentSeasonWinningRecord($game->team_id_home);
            $awayWinRecord = $this->eloCalculator->getCurrentSeasonWinningRecord($game->team_id_away);
            Log::info('Fetched Win Records', ['homeWinRecord' => $homeWinRecord, 'awayWinRecord' => $awayWinRecord]);

            $homeRatingWithRecord = $homeTeamRating * (1 + $homeWinRecord);
            $awayRatingWithRecord = $awayTeamRating * (1 + $awayWinRecord);
            Log::info('Ratings with Records', ['homeRatingWithRecord' => $homeRatingWithRecord, 'awayRatingWithRecord' => $awayRatingWithRecord]);

            $expectedHomeWinProbability = $this->eloCalculator->calculateExpectedScore(
                $homeRatingWithRecord,
                $awayRatingWithRecord,
                false
            );
            Log::info('Expected Home Win Probability', ['expectedHomeWinProbability' => $expectedHomeWinProbability]);

            $expectedAwayWinProbability = 1 - $expectedHomeWinProbability;
            Log::info('Expected Away Win Probability', ['expectedAwayWinProbability' => $expectedAwayWinProbability]);

            $expectedWins[$game->team_id_home] += $expectedHomeWinProbability;
            $expectedWins[$game->team_id_away] += $expectedAwayWinProbability;

            Log::info('Updated Expected Wins', [
                'team_id_home' => $game->team_id_home,
                'expected_wins_home' => $expectedWins[$game->team_id_home],
                'team_id_away' => $game->team_id_away,
                'expected_wins_away' => $expectedWins[$game->team_id_away],
            ]);
        }

        Log::info('Final Expected Wins', ['expectedWins' => $expectedWins]);
        return $expectedWins;
    }
}
