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

    public function __construct()
    {
        $this->apiKey = config('services.oddsapi.key');
        $this->baseUrl = config('services.oddsapi.base_url');
    }

    public function showOdds(Request $request)
    {
        $date = Carbon::parse($request->input('date', Carbon::today()->format('Y-m-d')));
        $odds = MlbOdds::whereDate('commence_time', $date)->get();

        return view('mlb.odds', [
            'sport' => 'MLB',
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
        $scores = $this->fetchScores();
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
}
