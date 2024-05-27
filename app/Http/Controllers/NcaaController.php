<?php

namespace App\Http\Controllers;

use App\Events\NcaaOddsFetched;
use App\Events\OddsFetched;
use App\Models\NcaaTeam;
use App\Services\NcaaOddsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Jobs\FetchNcaaOdds;

class NcaaController extends Controller
{
    protected $ncaaOddsService;

    public function __construct(NcaaOddsService $ncaaOddsService)
    {
        $this->ncaaOddsService = $ncaaOddsService;
    }

    public function showOdds(Request $request)
    {
        $sport = 'americanfootball_ncaaf';
        $markets = 'h2h,spreads,totals';

        // Fetch the odds data directly
        $odds = $this->ncaaOddsService->getOdds($sport, $markets);

        // Check for error in the response
        if (isset($odds['error_code'])) {
            return view('errors.quota', [
                'message' => $odds['message'],
                'details_url' => $odds['details_url'],
            ]);
        }

        // Ensure teams exist in the database
        foreach ($odds as $eventData) {
            $this->ensureTeamExists($eventData['home_team']);
            $this->ensureTeamExists($eventData['away_team']);
        }

        // Dispatch the job to fetch NCAA odds
        FetchNcaaOdds::dispatch($this->ncaaOddsService);

        // Dispatch the event to store the odds
        NcaaOddsFetched::dispatch($odds);

        Log::info("Odds API Response for {$sport}: " . json_encode($odds));

        return view('odds.show', compact('odds', 'sport'));
    }

    public function index()
    {
        // Fetch all NCAAF teams
        $teams = NcaaTeam::all();

        // Return the view with the teams data
        return view('ncaa.teams', compact('teams'));
    }

    private function ensureTeamExists($teamName)
    {
        NcaaTeam::firstOrCreate(['name' => $teamName]);
    }
}