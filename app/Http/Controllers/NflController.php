<?php

namespace App\Http\Controllers;

use App\Models\NflOdds;
use App\Models\NflTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class NflController extends Controller
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
        $odds = NflOdds::whereDate('commence_time', $date)->get();

        if ($odds->isEmpty()) {
            $errorMessage = 'No odds available at the moment.';
            Log::error($errorMessage);
            return view('nfl.odds', [
                'odds' => $odds,
                'sport' => 'NFL'
            ])->withErrors($errorMessage);
        }

        return view('nfl.odds', [
            'odds' => $odds,
            'sport' => 'NFL'
        ]);
    }

    public function index()
    {
        $teams = NflTeam::all();
        return view('nfl.teams', compact('teams'));
    }

    public function showScores(Request $request)
    {
        $scores = $this->fetchScores();
        return view('nfl.scores', compact('scores'));
    }

    protected function fetchScores()
    {
        $response = Http::get("{$this->baseUrl}/sports/americanfootball_nfl/scores", [
            'apiKey' => $this->apiKey,
            'daysFrom' => 3,
            'dateFormat' => 'iso',
        ]);

        return $response->successful() ? $response->json() : [];
    }

    public function filter(Request $request)
    {
        $routeName = strtolower('NFL') . '.odds'; // Ensure route name is in lowercase
        return redirect()->route($routeName, ['date' => $request->input('date')]);
    }
}
