<?php

namespace App\Http\Controllers;

use App\Models\NflOdds;
use App\Models\NflTeam;
use App\Services\NflScoresService;
use App\Services\NflOddsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NflController extends Controller
{
    protected $nflScoresService;
    protected $nflOddsService;
    protected $apiKey;
    protected $baseUrl;

    public function __construct(NflScoresService $nflScoresService, NflOddsService $nflOddsService)
    {
        $this->nflScoresService = $nflScoresService;
        $this->nflOddsService = $nflOddsService;
        $this->apiKey = config('services.oddsapi.key');
        $this->baseUrl = config('services.oddsapi.base_url');
    }

    public function index()
    {
        // Fetch all NFL teams
        $teams = NflTeam::all();

        // Return the view with the teams data
        return view('nfl.teams', compact('teams'));
    }

    public function showScores()
    {
        // Fetch scores using the service
        $scores = $this->fetchScores();

        return view('nfl.scores', compact('scores'));
    }

    public function showOdds(Request $request)
    {
        $sport = 'americanfootball_nfl';

        // Fetch the odds data from the database
        $odds = NflOdds::all();

        // Check if odds are empty
        if ($odds->isEmpty()) {
            $errorMessage = 'No odds available at the moment.';
            Log::error($errorMessage);
            return view('nfl.odds', compact('odds', 'sport'))->withErrors($errorMessage);
        }

        $sport_title = 'NFL';
        return view('nfl.odds', compact('odds', 'sport', 'sport_title'));
    }

    protected function fetchScores()
    {
        $response = Http::get("{$this->baseUrl}/sports/americanfootball_nfl/scores", [
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
