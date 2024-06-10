<?php

namespace App\Http\Controllers;

use App\Models\MlbOdds;
use App\Models\MlbScore;
use App\Models\MlbTeam;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MlbController extends Controller
{
    protected $sport = 'mlb';

    public function index()
    {
        $teams = MlbTeam::all();
        return view('mlb.teams', compact('teams'));
    }

    public function event(Request $request)
    {
        $selectedDate = $request->input('selectedDate', Carbon::today()->format('Y-m-d'));
        $scores = MlbScore::with('homeTeam', 'awayTeam')
            ->whereDate('commence_time', $selectedDate)
            ->get();
        $odds = MlbOdds::whereIn('event_id', $scores->pluck('event_id'))->get();

        return view('mlb.event', compact('scores', 'odds', 'selectedDate'));
    }
}
