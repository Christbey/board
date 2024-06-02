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

    public function showOdds(Request $request)
    {
        $sport = 'baseball_mlb';

        // Fetch the odds data from the database
        $odds = MlbOdds::all();

        // Check if odds are empty
        if ($odds->isEmpty()) {
            $errorMessage = 'No odds available at the moment.';
            Log::error($errorMessage);
            return view('mlb.odds', compact('odds', 'sport'))->withErrors($errorMessage);
        }

        return view('mlb.odds', compact('odds', 'sport'));
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
