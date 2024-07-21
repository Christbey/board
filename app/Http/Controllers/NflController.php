<?php

namespace App\Http\Controllers;

use App\Models\NflOdds;
use App\Models\NflScore;
use App\Models\NflTeam;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\Elo\EloRatingSystem;

// Adjust the namespace as needed

class NflController extends Controller
{
    protected mixed $apiKey;
    protected mixed $baseUrl;
    protected EloRatingSystem $eloRatingSystem;

    public function __construct(EloRatingSystem $eloRatingSystem)
    {
        $this->apiKey = config('services.oddsapi.key');
        $this->baseUrl = config('services.oddsapi.base_url');
        $this->eloRatingSystem = $eloRatingSystem; // Inject EloRatingSystem
    }

    public function index()
    {
        $teams = NflTeam::all();
        $expectedWins = $this->eloRatingSystem->calculateExpectedWinsForTeams();

        return view('nfl.teams', compact('teams', 'expectedWins'));
    }

    public function event(Request $request)
    {
        $selectedDate = $request->input('selectedDate', Carbon::today()->format('Y-m-d'));
        $scores = NflScore::with('homeTeam', 'awayTeam')
            ->whereDate('commence_time', $selectedDate)
            ->get();
        $odds = NflOdds::whereIn('event_id', $scores->pluck('event_id'))->get();

        return view('nfl.event', compact('scores', 'odds', 'selectedDate'));
    }
}
