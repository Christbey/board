<?php

namespace App\Http\Controllers;

use App\Models\NcaaScore;
use App\Models\NcaaOdds;
use App\Models\NcaaTeam;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NcaaController extends Controller
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
        $teams = NcaaTeam::all();
        return view('ncaa.teams', compact('teams'));
    }

    public function event(Request $request)
    {
        $selectedDate = $request->input('selectedDate', Carbon::today()->format('Y-m-d'));
        $scores = NcaaScore::with('homeTeam', 'awayTeam')
            ->whereDate('commence_time', $selectedDate)
            ->get();
        $odds = NcaaOdds::whereIn('event_id', $scores->pluck('event_id'))->get();
        return view('Ncaa.event', compact('scores', 'odds', 'selectedDate'));
    }
}
