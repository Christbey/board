<?php

namespace App\Http\Controllers;

use App\Events\OddsFetched;
use App\Models\NflTeam;
use App\Services\NflOddsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Jobs\FetchNflOdds;

class NflController extends Controller
{
    protected $nflOddsService;

    public function __construct(NflOddsService $nflOddsService)
    {
        $this->nflOddsService = $nflOddsService;
    }

    public function showOdds(Request $request)
    {
        $sport = 'americanfootball_nfl';
        $markets = 'h2h,spreads,totals';

        // Dispatch the job to fetch NFL odds
        FetchNflOdds::dispatch($this->nflOddsService);

        // Fetch the odds data directly
        $odds = $this->nflOddsService->getOdds($sport, $markets);

        // Check for error in the response
        if (isset($odds['error_code'])) {
            return view('errors.quota', [
                'message' => $odds['message'],
                'details_url' => $odds['details_url'],
            ]);
        }

        // Dispatch the event to store the odds
        OddsFetched::dispatch($odds);

        Log::info("Odds API Response for {$sport}: " . json_encode($odds));

        return view('nfl.odds', compact('odds', 'sport'));
    }

    public function index()
    {
        // Fetch all NFL teams
        $teams = NflTeam::all();

        // Return the view with the teams data
        return view('nfl.teams', compact('teams'));
    }
}
