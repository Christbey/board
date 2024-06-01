<?php

namespace App\Http\Controllers;

use App\Events\NcaaOddsFetched;
use App\Models\NcaaTeam;
use App\Services\NcaaOddsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Jobs\FetchNcaaOdds;

class NcaaController extends Controller
{
    protected $ncaaOddsService;
    protected $sport = 'americanfootball_ncaaf';
    protected $markets = 'h2h,spreads,totals';

    public function __construct(NcaaOddsService $ncaaOddsService)
    {
        $this->ncaaOddsService = $ncaaOddsService;
    }

    public function showOdds(Request $request)
    {
        // Fetch the odds data directly
        $odds = $this->ncaaOddsService->getOdds($this->sport, $this->markets);

        // Check for error in the response
        if (isset($odds['error_code'])) {
            return view('errors.quota', [
                'message' => $odds['message'],
                'details_url' => $odds['details_url'],
            ]);
        }

        // Ensure teams exist in the database
        $this->ensureTeamsExist($odds);

        // Dispatch the job to fetch NCAA odds
        FetchNcaaOdds::dispatch($this->ncaaOddsService);

        // Dispatch the event to store the odds
        NcaaOddsFetched::dispatch($odds);

        Log::info("Odds API Response for {$this->sport}: " . json_encode($odds));

        $sport = 'americanfootball_ncaaf';
        return view('odds.show', compact('odds', 'sport'));
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
