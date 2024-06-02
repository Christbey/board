<?php

namespace App\Http\Controllers;

use App\Models\NcaaOdds;
use App\Models\NcaaTeam;
use App\Services\NcaaOddsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NcaaController extends Controller
{
    protected $ncaaOddsService;
    protected $apiKey;
    protected $baseUrl;

    public function __construct(NcaaOddsService $ncaaOddsService)
    {
        $this->ncaaOddsService = $ncaaOddsService;
        $this->apiKey = config('services.oddsapi.key');
        $this->baseUrl = config('services.oddsapi.base_url');
    }

    public function showOdds(Request $request)
    {
        $sport = 'americanfootball_ncaaf';

        // Fetch the odds data from the database
        $odds = NcaaOdds::all();

        // Check if odds are empty
        if ($odds->isEmpty()) {
            $errorMessage = 'No odds available at the moment.';
            Log::error($errorMessage);
            return view('ncaa.odds', compact('odds', 'sport'))->withErrors($errorMessage);
        }

        return view('ncaa.odds', compact('odds', 'sport'));
    }

    public function index()
    {
        // Fetch all NCAAF teams
        $teams = NcaaTeam::all();

        // Return the view with the teams data
        return view('ncaa.teams', compact('teams'));
    }

    private function ensureTeamsExist(array $odds)
    {
        foreach ($odds as $eventData) {
            $this->ensureTeamExists($eventData['home_team']);
            $this->ensureTeamExists($eventData['away_team']);
        }
    }

    private function ensureTeamExists($teamName)
    {
        NcaaTeam::firstOrCreate(['name' => $teamName]);
    }
}
