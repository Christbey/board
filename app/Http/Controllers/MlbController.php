<?php

namespace App\Http\Controllers;

use App\Events\MlbOddsFetched;
use App\Jobs\FetchMlbOdds;
use App\Models\MlbTeam;
use App\Services\MlbOddsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MlbController extends Controller
{
    protected $mlbOddsService;

    public function __construct(MlbOddsService $mlbOddsService)
    {
        $this->mlbOddsService = $mlbOddsService;
    }

    public function showOdds(Request $request)
    {
        $sport = 'baseball_mlb';
        $markets = 'h2h,spreads,totals';

        // Dispatch the job to fetch MLB odds
        FetchMlbOdds::dispatch($this->mlbOddsService);

        // Fetch the odds data directly
        $odds = $this->mlbOddsService->getOdds($sport, $markets);

        // Check for error in the response
        if (isset($odds['error_code'])) {
            return view('errors.quota', [
                'message' => $odds['message'],
                'details_url' => $odds['details_url'],
            ]);
        }

        // Dispatch the event to store the odds
        MlbOddsFetched::dispatch($odds);

        Log::info("Odds API Response for {$sport}: " . json_encode($odds));

        return view('mlb.odds', compact('odds', 'sport'));
    }

    public function index()
    {
        // Fetch all MLB teams
        $teams = MlbTeam::all();

        // Return the view with the teams data
        return view('mlb.teams', compact('teams'));
    }
}