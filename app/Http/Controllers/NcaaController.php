<?php

namespace App\Http\Controllers;

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

        return view('ncaa.odds', [
            'sport' => 'NCAA',
            'odds' => $odds,
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
}
