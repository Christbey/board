<?php

namespace App\Http\Controllers;

use App\Models\NflTeam;
use App\Services\NflOddsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Events\OddsFetched;

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


            $odds = $this->nflOddsService->getOdds($sport, $markets);

            // Check for error in the response
            if (isset($odds['error_code'])) {
                return view('errors.quota', [
                    'message' => $odds['message'],
                    'details_url' => $odds['details_url'],
                ]);
            }

            Log::info("Odds API Response for {$sport}: " . json_encode($odds));

            // Dispatch the event to store odds
            event(new OddsFetched($odds));

            return view('nfl.odds', compact('odds', 'sport'));

        } /*catch (\Exception $e) {
            Log::error('An error occurred: ' . $e->getMessage());

            return view('errors.quota', [
                'message' => 'An unexpected error occurred.',
                'details_url' => 'https://the-odds-api.com/liveapi/guides/v4/api-error-codes.html#out-of-usage-credits',
            ]);
        }*/
    public function index()
    {
        // Fetch all NFL teams
        $teams = NflTeam::all();

        // Return the view with the teams data
        return view('nfl.teams', compact('teams'));
    }
}
