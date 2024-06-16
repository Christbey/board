<?php

namespace App\Http\Controllers;

use App\Models\NflOdds;
use App\Models\NflScore;
use App\Models\NflTeam;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NflController extends Controller
{
    protected mixed $apiKey;
    protected mixed $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.oddsapi.key');
        $this->baseUrl = config('services.oddsapi.base_url');
    }

    public function index()
    {
        $teams = NflTeam::all();
        return view('nfl.teams', compact('teams'));
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
