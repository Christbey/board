<?php

namespace App\Http\Controllers;

use App\Models\NflTeam;
use App\Models\NflTeamSchedule;
use App\Models\NflOdds;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\Elo\EloRatingSystem;
use Illuminate\Support\Facades\Log;

class NflController extends Controller
{
    protected string $apiKey;
    protected string $baseUrl;
    protected EloRatingSystem $eloRatingSystem;

    public function __construct(EloRatingSystem $eloRatingSystem)
    {
        $this->apiKey = config('services.oddsapi.key');
        $this->baseUrl = config('services.oddsapi.base_url');
        $this->eloRatingSystem = $eloRatingSystem;
    }

    public function index()
    {
        $teams = NflTeam::all();
        Log::info('Teams:', $teams->toArray());

        $expectedWins = $this->eloRatingSystem->calculateExpectedWinsForTeams();
        Log::info('Expected Wins:', $expectedWins);

        $seasonStartDate = Carbon::parse('2024-09-01');
        $seasonEndDate = Carbon::parse('2024-12-31');

        $nextOpponents = [];
        foreach ($teams as $team) {
            $schedules = NflTeamSchedule::where(function ($query) use ($team) {
                $query->where('team_id_home', $team->id)
                    ->orWhere('team_id_away', $team->id);
            })
                ->whereBetween('game_date', [$seasonStartDate, $seasonEndDate])
                ->whereNull('home_result')
                ->whereNull('away_result')
                ->orderBy('game_date')
                ->take(5)
                ->get(['id', 'game_date', 'home', 'away', 'team_id_home', 'team_id_away']);
            Log::info('Schedules for team ' . $team->id, $schedules->toArray());

            foreach ($schedules as $schedule) {
                // Generate composite key using the method in the model
                $compositeKey = NflTeamSchedule::generateCompositeKey($schedule);

                // Fetch odds using the composite key
                $odds = NflOdds::where('composite_key', $compositeKey)->first(['spread_home_point', 'spread_away_point']);

                $schedule->composite_key = $compositeKey; // Store the composite key in the schedule
                $schedule->spread_home = $odds?->spread_home_point;
                $schedule->spread_away = $odds?->spread_away_point;
            }

            $nextOpponents[$team->id] = $schedules;
        }

        Log::info('Next Opponents:', $nextOpponents);

        return view('nfl.teams', compact('teams', 'expectedWins', 'nextOpponents'));
    }

    public function show($teamId, EloRatingSystem $eloRatingSystem)
    {
        Log::info('Showing team with ID:', ['teamId' => $teamId]);

        $team = NflTeam::findOrFail($teamId);
        Log::info('Team:', ['team' => $team]);

        $expectedWins = $eloRatingSystem->calculateExpectedWins($team->id);
        Log::info('Expected Wins:', ['expectedWins' => $expectedWins]);

        return view('nfl.show', compact('team', 'expectedWins'));
    }
}
