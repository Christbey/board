<?php

namespace App\Http\Controllers;

use App\Events\NbaOddsFetched;
use App\Jobs\FetchNbaOdds;
use App\Models\NbaTeam;
use App\Services\NbaOddsService;
use App\Services\NbaScoreService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NbaController extends Controller
{
    protected $nbaOddsService;
    protected $nbaScoreService;
    protected $sport = 'basketball_nba';
    protected $markets = 'h2h,spreads,totals';

    public function __construct(NbaOddsService $nbaOddsService, NbaScoreService $nbaScoreService)
    {
        $this->nbaOddsService = $nbaOddsService;
        $this->nbaScoreService = $nbaScoreService;
    }

    public function showOdds(Request $request)
    {
        // Dispatch the job to fetch NBA odds
        FetchNbaOdds::dispatch($this->nbaOddsService);

        // Fetch the odds data directly
        $odds = $this->nbaOddsService->getOdds($this->sport, $this->markets);

        // Check for error in the response
        if (isset($odds['error_code'])) {
            return view('errors.quota', [
                'message' => $odds['message'],
                'details_url' => $odds['details_url'],
            ]);
        }

        // Dispatch the event to store the odds
        NbaOddsFetched::dispatch($odds);

        Log::info("Odds API Response for {$this->sport}", $odds);

        $sport = 'basketball_nba';
        return view('nba.odds', compact('odds', 'sport'));
    }

    public function index()
    {
        // Fetch all NBA teams
        $teams = NbaTeam::all();

        // Return the view with the teams data
        return view('nba.teams', compact('teams'));
    }

    public function showScores(Request $request)
    {
        // Log to ensure this method is being called
        Log::info('showScores method called.');

        // Fetch the scores data directly
        $scores = $this->nbaScoreService->getScores();

        // Log the fetched scores
        Log::info('Fetched NBA Scores', $scores);

        // Return the view with the scores data
        return view('nba.scores', compact('scores'));
    }
}
