<?php

namespace App\Http\Controllers;

use App\Models\NcaaScore;
use App\Models\NcaaOdds;
use App\Models\NcaaTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class NcaaController extends Controller
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
        $odds = NcaaOdds::whereDate('commence_time', $date)->get();

        if ($odds->isEmpty()) {
            $errorMessage = 'No odds available at the moment.';
            Log::error($errorMessage);
            return view('ncaa.odds', [
                'odds' => $odds,
                'sport' => 'NCAA'
            ])->withErrors($errorMessage);
        }

        return view('ncaa.odds', [
            'odds' => $odds,
            'sport' => 'NCAA'
        ]);
    }

    public function index()
    {
        $teams = NcaaTeam::all();
        return view('ncaa.teams', compact('teams'));
    }

    public function showScores(Request $request)
    {
        $scores = $this->fetchScores();
        return view('ncaa.scores', compact('scores'));
    }

    protected function fetchScores()
    {
        $response = Http::get("{$this->baseUrl}/sports/americanfootball_ncaaf/scores", [
            'apiKey' => $this->apiKey,
            'daysFrom' => 3,
            'dateFormat' => 'iso',
        ]);

        return $response->successful() ? $response->json() : [];
    }

    public function filter(Request $request)
    {
        $routeName = strtolower($this->sport) . '.odds'; // Ensure route name is in lowercase
        return redirect()->route($routeName, ['date' => $request->input('date')]);
    }

    public function show(Request $request)
    {
        $selectedDate = $request->input('selectedDate', Carbon::today()->format('Y-m-d'));
        $scores = NcaaScore::with('homeTeam', 'awayTeam')
            ->whereDate('commence_time', $selectedDate)
            ->get();
        $odds = NcaaOdds::whereIn('event_id', $scores->pluck('event_id'))->get();

        return view('Ncaa.show', compact('scores', 'odds', 'selectedDate'));
    }
}
