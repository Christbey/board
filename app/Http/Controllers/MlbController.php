<?php

// app/Http/Controllers/MlbController.php

namespace App\Http\Controllers;

use App\Models\MlbTeam;
use App\Services\MlbOddsService;
use App\Events\OddsFetched;
use App\Jobs\FetchMlbOdds;
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

        $odds = $this->mlbOddsService->getOdds($sport, $markets);


            // Dispatch job to store the odds
            OddsFetched::dispatch($odds);

            Log::info("Odds API Response for {$sport}: " . json_encode($odds));

            return view('odds.show', compact('odds', 'sport'));



    }

    public function index()
    {
        // Fetch all MLB teams
        $teams = MlbTeam::all();

        // Return the view with the teams data
        return view('mlb.teams', compact('teams'));
    }
}
