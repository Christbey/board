<?php

namespace App\Http\Controllers;

use App\Models\NbaOdds;
use App\Models\NbaTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
        $sport = 'basketball_nba';
        $odds = NbaOdds::all();

        if ($odds->isEmpty()) {
            $errorMessage = 'No odds available at the moment.';
            Log::error($errorMessage);
            return view('nba.odds', compact('odds', 'sport'))->withErrors($errorMessage);
        }

        return view('nba.odds', compact('odds', 'sport'));
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
}
