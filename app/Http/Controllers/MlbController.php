<?php

namespace App\Http\Controllers;

use App\Events\MlbOddsFetched;
use App\Jobs\FetchMlbOdds;
use App\Models\MlbOdds;
use App\Models\MlbTeam;
use App\Services\MlbOddsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MlbController extends Controller
{
    protected $mlbOddsService;
    protected $apiKey;
    protected $baseUrl;

    public function __construct(MlbOddsService $mlbOddsService)
    {
        $this->mlbOddsService = $mlbOddsService;
        $this->apiKey = config('services.oddsapi.key');
        $this->baseUrl = config('services.oddsapi.base_url');
    }

    public function showOdds()
    {
        $today = Carbon::today();
        $odds = MLBOdds::whereDate('commence_time', $today)->get();

        return view('mlb.odds', [
            'odds' => $odds,
            'sport' => 'MLB' // Or fetch dynamically as needed
        ]);
    }

    public function index()
    {
        // Fetch all MLB teams
        $teams = MlbTeam::all();

        // Return the view with the teams data
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

        if ($response->successful()) {
            return $response->json();
        }

        // Handle the case where the API request fails
        return [];
    }
}
