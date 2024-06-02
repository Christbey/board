<?php

namespace App\Http\Controllers;

use App\Models\NbaOdds;
use App\Models\NbaTeam;
use App\Services\NbaOddsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NbaController extends Controller
{
    protected $nbaOddsService;
    protected $apiKey;
    protected $baseUrl;

    public function __construct(NbaOddsService $nbaOddsService)
    {
        $this->nbaOddsService = $nbaOddsService;
        $this->apiKey = config('services.oddsapi.key');
        $this->baseUrl = config('services.oddsapi.base_url');
    }

    public function showOdds(Request $request)
    {
        $sport = 'basketball_nba';

        // Fetch the odds data from the database
        $odds = NbaOdds::all();

        // Check if odds are empty
        if ($odds->isEmpty()) {
            $errorMessage = 'No odds available at the moment.';
            Log::error($errorMessage);
            return view('nba.odds', compact('odds', 'sport'))->withErrors($errorMessage);
        }

        return view('nba.odds', compact('odds', 'sport'));
    }

    public function index()
    {
        // Fetch all NBA teams
        $teams = NbaTeam::all();

        // Return the view with the teams data
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

        if ($response->successful()) {
            return $response->json();
        }

        // Handle the case where the API request fails
        return [];
    }
}
