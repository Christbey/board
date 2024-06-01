<?php

namespace App\Http\Controllers;

use App\Events\MlbOddsFetched;
use App\Jobs\FetchMlbOdds;
use App\Models\MlbTeam;
use App\Services\MlbOddsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MlbController extends Controller
{
    protected $mlbOddsService;
    protected $apiKey;
    protected $baseUrl;

    public function __construct(MlbOddsService $mlbOddsService)
    {
        $this->mlbOddsService = $mlbOddsService;
        $this->apiKey = config('services.oddsapi.key');
        $this->baseUrl = config('services.oddsapi.base_url');
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

    public function showScores(Request $request)
    {
        $scores = $this->fetchScores();

        return view('mlb.scores', compact('scores'));
    }

    protected function fetchScores()
    {
        $response = Http::get("{$this->baseUrl}/sports/baseball_mlb/scores", [
            'apiKey' => $this->apiKey,
            'daysFrom' => 3,
            'dateFormat' => 'iso',
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        // Handle the case where the API request fails
        return [];
    }
}
