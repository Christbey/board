<?php

namespace App\Http\Controllers;

use App\Models\MlbOdds;
use App\Models\MlbScore;
use App\Models\MlbTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MlbController extends Controller
{
    protected $apiKey;
    protected $baseUrl;
    protected $sport;

    public function __construct()
    {
        $this->apiKey = config('services.oddsapi.key');
        $this->baseUrl = config('services.oddsapi.base_url');
        $this->sport = 'mlb';
    }

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
                if ($score->completed) {
                    return PHP_INT_MAX; // Completed events are sorted last
                } elseif ($score->home_team_score !== null && $score->away_team_score !== null) {
                    return Carbon::parse($score->commence_time)->timestamp; // Live events are sorted by start time
                } else {
                    return PHP_INT_MAX - 1; // Upcoming events
                }
            });

        return view('mlb.scores', compact('scores'));
    }


    protected function fetchScores()
    {
        $response = Http::get("{$this->baseUrl}/sports/baseball_mlb/scores", [
            'apiKey' => $this->apiKey,
            'daysFrom' => 3,
            'dateFormat' => 'iso',
        ]);

        return $response->successful() ? $response->json() : [];
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
