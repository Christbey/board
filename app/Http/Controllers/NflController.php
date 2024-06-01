<?php

namespace App\Http\Controllers;

use App\Events\NflOddsFetched;
use App\Jobs\FetchNflOdds;
use App\Models\NflTeam;
use App\Services\NFLScoresService;
use App\Services\NflOddsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NflController extends Controller
{
    protected $nflScoresService;
    protected $nflOddsService;
    protected $sport = 'americanfootball_nfl';
    protected $markets = 'h2h,spreads,totals';

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
        // Fetch scores using the service
        $scores = $this->nflScoresService->fetchScores();

        if (empty($scores)) {
            $errorMessage = 'No scores available or there was an error fetching the scores.';
            Log::error($errorMessage);
            return view('nfl.scores', compact('scores', 'errorMessage'));
        }

        return view('nfl.scores', compact('scores'));
    }

    public function showOdds(Request $request)
    {
        // Dispatch the job to fetch NFL odds
        FetchNflOdds::dispatch($this->nflOddsService);

        // Fetch the odds data directly
        $odds = $this->nflOddsService->getOdds($this->sport, $this->markets);

        // Check for error in the response
        if (isset($odds['error_code'])) {
            return view('errors.quota', [
                'message' => $odds['message'],
                'details_url' => $odds['details_url'],
            ]);
        }

        // Dispatch the event to store the odds
        NflOddsFetched::dispatch($odds);

        Log::info("Odds API Response for {$this->sport}: " . json_encode($odds));

        $sport = 'americanfootball_nfl';
        $sport_title = 'NFL';
        return view('nfl.odds', compact('odds', 'sport', 'sport_title'));
    }
}
