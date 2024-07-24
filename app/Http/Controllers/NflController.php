<?php

namespace App\Http\Controllers;

use App\Models\NflTeam;
use App\Models\NflTeamSchedule;
use App\Services\NflPredictionService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

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
            Log::info('Expected Wins Data:', $data['expectedWins']);
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

            // Calculate expected wins
            $expectedWins = $this->nflPredictionService->eloRatingSystem->calculateExpectedWinsForTeams();

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
            Log::info('Next Opponents:', $nextOpponents->toArray());

            return compact('teams', 'expectedWins', 'nextOpponents');
        } catch (Exception $e) {
            Log::error('Error fetching teams with schedules and odds: ' . $e->getMessage(), [
                'exception' => $e,
            ]);
            throw new Exception('Unable to fetch teams with schedules and odds');
        }
    }


    public function show($teamId)
    {
        try {
            $team = NflTeam::with(['schedules' => function ($query) {
                $seasonStartDate = Carbon::parse('2024-09-01');
                $seasonEndDate = Carbon::parse('2024-12-31');
                $query->whereBetween('game_date', [$seasonStartDate, $seasonEndDate])
                    ->whereNull('home_result')
                    ->whereNull('away_result')
                    ->orderBy('game_date');
            }])->findOrFail($teamId);

            $expectedWins = $this->nflPredictionService->eloRatingSystem->calculateExpectedWins($team->id);
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

            return view('nfl.show', compact('team', 'expectedWins', 'nextOpponents'));
        } catch (Exception $e) {
            Log::error('Error fetching team details: ' . $e->getMessage(), [
                'exception' => $e,
            ]);
            return response()->view('errors.500', [], 500);
        }
    }
}
