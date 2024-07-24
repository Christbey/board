<?php

namespace App\Services;

use App\Models\NflPrediction;
use App\Models\NflTeam;
use App\Models\NflTeamSchedule;
use App\Models\NflOdds;
use App\Services\Elo\EloRatingSystem;
use Carbon\Carbon;
use Log;

class NflPredictionService
{
    public EloRatingSystem $eloRatingSystem;

    public function __construct(EloRatingSystem $eloRatingSystem)
    {
        $this->eloRatingSystem = $eloRatingSystem;
    }

    public function getTeamsWithSchedulesAndOdds()
    {
        $teams = NflTeam::with(['schedules' => function ($query) {
            $seasonStartDate = Carbon::parse('2024-09-01');
            $seasonEndDate = Carbon::parse('2024-12-31');
            $query->whereBetween('game_date', [$seasonStartDate, $seasonEndDate])
                ->whereNull('home_result')
                ->whereNull('away_result')
                ->orderBy('game_date');
        }])->get();

        $expectedWins = $this->eloRatingSystem->calculateExpectedWinsForTeams();

        $schedules = $teams->flatMap->schedules;

        // Generate composite keys for all schedules
        $compositeKeys = $schedules->map(function ($schedule) {
            return NflTeamSchedule::generateCompositeKey($schedule);
        });

        // Fetch all odds in a single query
        $odds = NflOdds::whereIn('composite_key', $compositeKeys)->get()->keyBy('composite_key');

        // Attach the odds to the schedules
        $schedules->each(function ($schedule) use ($odds) {
            $compositeKey = NflTeamSchedule::generateCompositeKey($schedule);
            $schedule->spread_home = $odds->get($compositeKey)->spread_home_point ?? null;
            $schedule->spread_away = $odds->get($compositeKey)->spread_away_point ?? null;
        });

        $nextOpponents = $teams->mapWithKeys(function ($team) {
            $schedules = NflTeamSchedule::where('team_id_home', $team->id)
                ->orWhere('team_id_away', $team->id)
                ->whereBetween('game_date', [Carbon::now(), Carbon::parse('2024-12-31')])
                ->orderBy('game_date')
                ->take(3)
                ->get();

            return [$team->id => $schedules->map(function ($schedule) use ($team) {
                $opponentId = $schedule->team_id_home === $team->id ? $schedule->team_id_away : $schedule->team_id_home;
                $opponent = NflTeam::find($opponentId);

                // Ensure game_date is a Carbon instance
                $gameDate = Carbon::parse($schedule->game_date);

                return [
                    'id' => $opponent->id,
                    'name' => $opponent->name,
                    'game_date' => $gameDate->format('Y-m-d'),
                ];
            })];
        });

        // Log nextOpponents for debugging
        Log::info('Next Opponents:', $nextOpponents->toArray());

        return compact('teams', 'expectedWins', 'nextOpponents');
    }

    public function logPredictedScores()
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

        return 'Predicted scores logged successfully.';
    }

    private function logExpectedWinningPercentageAndPredictedScore(NflTeamSchedule $game, $homeStadium, $awayStadium): void
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
