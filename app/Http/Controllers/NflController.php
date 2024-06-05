<?php

namespace App\Http\Controllers;

use App\Models\NflOdds;
use App\Models\NflTeam;
use App\Services\NflScoresService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NflController extends Controller
{
    protected $nflScoresService;

    public function __construct(NflScoresService $nflScoresService)
    {
        $this->nflScoresService = $nflScoresService;
    }

    public function index()
    {
        $teams = NflTeam::all();
        return view('nfl.teams', compact('teams'));
    }

    public function showScores()
    {
        $scores = $this->fetchScores();
        return view('nfl.scores', compact('scores'));
    }

    public function showOdds(Request $request)
    {
        $date = Carbon::parse($request->input('date', Carbon::today()->format('Y-m-d')));
        $odds = NflOdds::whereDate('commence_time', $date)->get();

        return view('nfl.odds', [
            'sport' => 'NFL',
            'odds' => $odds,
        ]);
    }

    protected function fetchScores()
    {
        $response = Http::get("{$this->getBaseUrl()}/sports/americanfootball_nfl/scores", [
            'apiKey' => $this->getApiKey(),
            'daysFrom' => 3,
            'dateFormat' => 'iso',
        ]);

        return $response->successful() ? $response->json() : [];
    }

    protected function getApiKey()
    {
        return config('services.oddsapi.key');
    }

    protected function getBaseUrl()
    {
        return config('services.oddsapi.base_url');
    }
}
