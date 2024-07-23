<?php

namespace App\Http\Controllers;

use App\Models\NflTeam;
use App\Models\NflTeamSchedule;
use App\Models\NflOdds;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\Elo\EloRatingSystem;

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
        $expectedWins = $this->eloRatingSystem->calculateExpectedWinsForTeams();

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

            foreach ($schedules as $schedule) {
                // Generate composite key using the method in the model
                $compositeKey = NflTeamSchedule::generateCompositeKey($schedule);

                // Fetch odds using the composite key
                $odds = NflOdds::where('composite_key', $compositeKey)->first(['spread_home_point', 'spread_away_point']);

                $schedule->composite_key = $compositeKey; // Store the composite key in the schedule
                $schedule->spread_home = $odds ? $odds->spread_home_point : null;
                $schedule->spread_away = $odds ? $odds->spread_away_point : null;
            }

            $nextOpponents[$team->id] = $schedules;
        }

        return view('nfl.teams', compact('teams', 'expectedWins', 'nextOpponents'));
    }


    public function show($teamId, EloRatingSystem $eloRatingSystem)
    {
        $team = NflTeam::findOrFail($teamId);
        $expectedWins = $eloRatingSystem->calculateExpectedWins($team->id);
        return view('nfl.show', compact('team', 'expectedWins'));
    }


}
