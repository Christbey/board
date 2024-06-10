<?php

namespace App\Http\Controllers;

use App\Models\MlbOdds;
use App\Models\MlbScore;
use App\Models\MlbTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MlbController extends Controller
{
    protected $sport = 'mlb';

    public function showOdds(Request $request)
    {
        $date = Carbon::parse($request->input('date', Carbon::today()->format('Y-m-d')));
        $odds = MlbOdds::whereDate('commence_time', $date)->get();

        if ($odds->isEmpty()) {
            $errorMessage = 'No odds available at the moment.';
            Log::error($errorMessage);
            return view('odds.show', [
                'sport' => strtoupper($this->sport),
                'odds' => $odds,
            ])->withErrors($errorMessage);
        }

        return view('odds.show', [
            'sport' => strtoupper($this->sport),
            'odds' => $odds,
        ]);
    }

    public function index()
    {
        $teams = MlbTeam::all();
        return view('mlb.teams', compact('teams'));
    }

    public function showScores(Request $request)
    {
        $scores = MlbScore::with(['homeTeam', 'awayTeam'])
            ->get()
            ->sortBy(function($score) {
                return $score->completed ? PHP_INT_MAX : Carbon::parse($score->commence_time)->timestamp;
            });

        return view('mlb.scores', compact('scores'));
    }

    public function filter(Request $request)
    {
        return redirect()->route('odds.show', ['mlb' => $this->sport, 'date' => $request->input('date')]);
    }

    public function show(Request $request)
    {
        $selectedDate = $request->input('selectedDate', Carbon::today()->format('Y-m-d'));
        $scores = MlbScore::with('homeTeam', 'awayTeam')
            ->whereDate('commence_time', $selectedDate)
            ->get();
        $odds = MlbOdds::whereIn('event_id', $scores->pluck('event_id'))->get();

        return view('mlb.show', compact('scores', 'odds', 'selectedDate'));
    }
}
