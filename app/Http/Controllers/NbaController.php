<?php

namespace App\Http\Controllers;

use App\Events\OddsFetched;
use App\Models\NbaTeam;
use App\Services\NbaOddsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Jobs\FetchNbaOdds;

class NbaController extends Controller
{
    protected $nbaOddsService;

    public function __construct(NbaOddsService $nbaOddsService)
    {
        $this->nbaOddsService = $nbaOddsService;
    }

    public function showOdds(Request $request)
    {
        $sport = 'basketball_nba';
        $markets = 'h2h,spreads,totals';

        FetchNbaOdds::dispatch($this->nbaOddsService);

        $odds = $this->nbaOddsService->getOdds($sport, $markets);

        // Check for error in the response
        if (isset($odds['error_code'])) {
            return view('errors.quota', [
                'message' => $odds['message'],
                'details_url' => $odds['details_url'],
            ]);
        }

        OddsFetched::dispatch($odds);

        Log::info("Odds API Response for {$sport}: " . json_encode($odds));

        return view('odds.show', compact('odds', 'sport'));
    }

    public function index()
    {
        // Fetch all NBA teams
        $teams = NbaTeam::all();

        // Return the view with the teams data
        return view('nba.teams', compact('teams'));
    }
}
