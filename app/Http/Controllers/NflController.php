<?php

namespace App\Http\Controllers;

use App\Events\NflOddsFetched;
use App\Jobs\FetchNFLScores;
use App\Jobs\FetchNflOdds;
use App\Models\NflTeam;
use App\Services\NFLScoresService;
use App\Services\NflOddsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

class NflController extends Controller
{
    protected $nflScoresService;
    protected $nflOddsService;

    public function __construct(NFLScoresService $nflScoresService, NflOddsService $nflOddsService)
    {
        $this->nflScoresService = $nflScoresService;
        $this->nflOddsService = $nflOddsService;
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
        // Dispatch the job to fetch scores and get the scores from the job
        $scores = Bus::dispatchNow(new FetchNFLScores())->scores;

        if (empty($scores)) {
            $errorMessage = 'No scores available or there was an error fetching the scores.';
            Log::error($errorMessage);
            return view('nfl.scores', compact('scores', 'errorMessage'));
        }

        return view('nfl.scores', compact('scores'));
    }

    public function showOdds(Request $request)
    {
        $sport = 'americanfootball_nfl';
        $markets = 'h2h,spreads,totals';

        // Dispatch the job to fetch NFL odds and fetch the odds data directly
        $odds = Bus::dispatchNow(new FetchNflOdds($this->nflOddsService))->odds ?? $this->nflOddsService->getOdds($sport, $markets);

        // Check for error in the response
        if (isset($odds['error_code'])) {
            return view('errors.quota', [
                'message' => $odds['message'],
                'details_url' => $odds['details_url'],
            ]);
        }

        // Dispatch the event to store the odds
        NflOddsFetched::dispatch($odds);

        Log::info("Odds API Response for {$sport}: " . json_encode($odds));

        return view('nfl.odds', compact('odds', 'sport'));
    }
}
