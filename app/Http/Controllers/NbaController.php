<?php

namespace App\Http\Controllers;

use App\Models\NbaOdds;
use App\Models\NbaScore;
use App\Models\NbaTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class NbaController extends Controller
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.oddsapi.key');
        $this->baseUrl = config('services.oddsapi.base_url');
    }

    public function showOdds(Request $request)
    {
        $date = Carbon::parse($request->input('date', Carbon::today()->format('Y-m-d')));
        $odds = NbaOdds::whereDate('commence_time', $date)->get();

        if ($odds->isEmpty()) {
            $errorMessage = 'No odds available for the selected date.';
            Log::error($errorMessage);
            return view('odds.show', [
                'odds' => $odds,
                'sport' => 'NBA'
            ])->withErrors($errorMessage);
        }

        return view('odds.show', [
            'odds' => $odds,
            'sport' => 'NBA'
        ]);
    }

    public function index()
    {
        $teams = NbaTeam::all();
        return view('nba.teams', compact('teams'));
    }

    public function showScores(Request $request)
    {
        $scores = $this->fetchScores();
        return view('nba.scores', compact('scores'));
    }

    protected function fetchScores()
    {
        $response = Http::get("{$this->baseUrl}/sports/basketball_nba/scores", [
            'apiKey' => $this->apiKey,
            'daysFrom' => 3,
            'dateFormat' => 'iso',
        ]);

        return $response->successful() ? $response->json() : [];
    }

    public function filter(Request $request)
    {
        $routeName = 'nba.odds'; // Ensure route name is correct
        return redirect()->route($routeName, ['date' => $request->input('date')]);
    }

    public function show(Request $request)
    {
        $selectedDate = $request->input('selectedDate', Carbon::today()->format('Y-m-d'));
        $scores = NbaScore::with('homeTeam', 'awayTeam')
            ->whereDate('commence_time', $selectedDate)
            ->get();
        $odds = NbaOdds::whereIn('event_id', $scores->pluck('event_id'))->get();

        return view('nba.show', compact('scores', 'odds', 'selectedDate'));
    }

}
