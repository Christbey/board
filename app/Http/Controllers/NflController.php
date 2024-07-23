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
        $teams = NflTeam::with(['schedules' => function ($query) {
            $seasonStartDate = Carbon::parse('2024-09-01');
            $seasonEndDate = Carbon::parse('2024-12-31');
            $query->whereBetween('game_date', [$seasonStartDate, $seasonEndDate])
                ->whereNull('home_result')
                ->whereNull('away_result')
                ->orderBy('game_date')
                ->take(5);
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
            return [$team->id => $team->schedules];
        });

        return view('nfl.teams', compact('teams', 'expectedWins', 'nextOpponents'));
    }

    public function show($teamId, EloRatingSystem $eloRatingSystem)
    {
        $team = NflTeam::findOrFail($teamId);
        $expectedWins = $eloRatingSystem->calculateExpectedWins($team->id);
        return view('nfl.show', compact('team', 'expectedWins'));
    }
}
