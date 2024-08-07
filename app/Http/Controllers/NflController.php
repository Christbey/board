<?php

namespace App\Http\Controllers;

use App\Models\NflPrediction;
use App\Models\NflTeam;
use App\Models\NflTeamSchedule;
use App\Services\NflPredictionService;
use App\Helpers\FormatHelper;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use App\Helpers\NflHelper;

class NflController extends Controller
{
    protected NflPredictionService $nflPredictionService;

    public function __construct(NflPredictionService $nflPredictionService)
    {
        $this->nflPredictionService = $nflPredictionService;
    }

    public function index()
    {
        try {
            $data = $this->getTeamsWithSchedulesAndOdds();
            Log::info('Expected Wins Data:', ['expectedWins' => $data['expectedWins']]);
            return view('nfl.teams', $data);
        } catch (Exception $e) {
            Log::error('Error fetching teams with schedules and odds: ' . $e->getMessage(), [
                'exception' => $e,
            ]);
            return response()->view('errors.500', [], 500);
        }
    }

    public function getTeamsWithSchedulesAndOdds()
    {
        try {
            $teams = NflTeam::all();

            // Calculate expected wins from the nfl_predictions table
            $expectedWins = NflPrediction::selectRaw('team_id_home, SUM(home_win_percentage / 100) as home_expected_wins')
                ->groupBy('team_id_home')
                ->pluck('home_expected_wins', 'team_id_home')
                ->toArray();

            $awayExpectedWins = NflPrediction::selectRaw('team_id_away, SUM(away_win_percentage / 100) as away_expected_wins')
                ->groupBy('team_id_away')
                ->pluck('away_expected_wins', 'team_id_away')
                ->toArray();

            // Combine home and away expected wins
            foreach ($awayExpectedWins as $teamId => $wins) {
                if (isset($expectedWins[$teamId])) {
                    $expectedWins[$teamId] += $wins;
                } else {
                    $expectedWins[$teamId] = $wins;
                }
            }

            // Fetch schedules directly from nfl_team_schedules
            $schedules = NflTeamSchedule::whereBetween('game_date', [Carbon::parse('2024-09-01'), Carbon::parse('2024-12-31')])
                ->where('game_status', 'scheduled')
                ->where('season_type', 'Regular Season')
                ->orderBy('game_date')
                ->get();

            // Log schedules for debugging
            Log::info('Schedules:', $schedules->toArray());

            // Map next opponents
            $nextOpponents = $teams->mapWithKeys(function ($team) use ($schedules) {
                $teamSchedules = $schedules->filter(function ($schedule) use ($team) {
                    return $schedule->team_id_home == $team->id || $schedule->team_id_away == $team->id;
                })->take(3)->map(function ($schedule) use ($team) {
                    $opponentId = $schedule->team_id_home == $team->id ? $schedule->team_id_away : $schedule->team_id_home;
                    $opponent = NflTeam::find($opponentId);

                    if (!$opponent) {
                        return null;
                    }

                    // Ensure game_date is a Carbon instance
                    $gameDate = Carbon::parse($schedule->game_date);

                    return [
                        'id' => $opponent->id,
                        'name' => $opponent->name,
                        'game_date' => $gameDate->format('Y-m-d'),
                    ];
                })->filter(); // Remove null values

                return [$team->id => $teamSchedules];
            });

            // Log nextOpponents for debugging
            Log::info('Next Opponents:', ['nextOpponents' => $nextOpponents]);

            return compact('teams', 'expectedWins', 'nextOpponents');
        } catch (Exception $e) {
            Log::error('Error fetching teams with schedules and odds: ' . $e->getMessage(), [
                'exception' => $e,
            ]);
            throw new Exception('Unable to fetch teams with schedules and odds');
        }
    }

    public function show($teamId, Request $request)
    {
        try {
            $position = $request->input('position');

            // Get current season date range
            $currentSeasonYear = Carbon::now()->year;
            [$currentSeasonStartDate, $currentSeasonEndDate] = NflHelper::getSeasonDateRange($currentSeasonYear);

            // Get previous season date range
            $previousSeasonYear = $currentSeasonYear - 1;
            [$previousSeasonStartDate, $previousSeasonEndDate] = NflHelper::getSeasonDateRange($previousSeasonYear);

            $team = NflTeam::with(['schedules' => function ($query) use ($currentSeasonStartDate, $currentSeasonEndDate) {
                $query->whereBetween('game_date', [$currentSeasonStartDate, $currentSeasonEndDate])
                    ->whereNull('home_result')
                    ->whereNull('away_result')
                    ->where('season_type', 'Regular Season')
                    ->orderBy('game_date');
            }, 'players' => function ($query) use ($position) {
                if ($position) {
                    $query->where('pos', $position);
                }
            }])->findOrFail($teamId);

            // Debugging code
            Log::info('Team Players:', ['players' => $team->players->toArray()]);

            $predictions = NflPrediction::where('team_id_home', $team->id)
                ->orWhere('team_id_away', $team->id)
                ->get();

            $expectedWins = $predictions->sum(function ($prediction) use ($team) {
                return ($prediction->team_id_home == $team->id ? $prediction->home_win_percentage : $prediction->away_win_percentage) / 100;
            });

            $predictions->each(function ($prediction) {
                [$homeScore, $awayScore] = FormatHelper::calculateScoreBasedOnWinPercentage($prediction->home_win_percentage, $prediction->away_win_percentage);
                $prediction->home_pts_prediction = $homeScore;
                $prediction->away_pts_prediction = $awayScore;
            });

            $nextOpponents = $team->schedules->take(3)->map(function ($schedule) use ($team) {
                $opponentId = $schedule->team_id_home === $team->id ? $schedule->team_id_away : $schedule->team_id_home;
                $opponent = NflTeam::find($opponentId);

                // Ensure game_date is a Carbon instance
                $gameDate = Carbon::parse($schedule->game_date);

                return [
                    'id' => $opponent->id,
                    'name' => $opponent->name,
                    'game_date' => $gameDate->format('Y-m-d'),
                ];
            });

            // Calculate the wins and losses for the team for the current and previous seasons
            $currentSeasonRecord = NflTeamSchedule::calculateWinsAndLosses($team->id, $currentSeasonStartDate, $currentSeasonEndDate);
            $previousSeasonRecord = NflTeamSchedule::calculateWinsAndLosses($team->id, $previousSeasonStartDate, $previousSeasonEndDate);

            // Get all positions for the filter
            $positions = NflTeam::findOrFail($teamId)->players()->distinct()->pluck('pos')->toArray();

            // Get players
            $players = $team->players;

            return view('nfl.show', compact('team', 'expectedWins', 'nextOpponents', 'predictions', 'currentSeasonRecord', 'previousSeasonRecord', 'players', 'positions', 'position'));
        } catch (Exception $e) {
            Log::error('Error fetching team details: ' . $e->getMessage(), [
                'exception' => $e,
            ]);
            return response()->view('errors.500', [], 500);
        }
    }
}
