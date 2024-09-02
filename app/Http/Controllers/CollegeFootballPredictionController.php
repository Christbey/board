<?php

namespace App\Http\Controllers;

use App\Services\CollegeFootball\CollegeFootballPredictionService;
use DB;
use Illuminate\Http\Request;

class CollegeFootballPredictionController extends Controller
{
    protected CollegeFootballPredictionService $predictionService;

    public function __construct(CollegeFootballPredictionService $predictionService)
    {
        $this->predictionService = $predictionService;
    }

    public function index(Request $request)
    {
        // Fetch weeks from the prediction service for the year 2024
        $weeks = $this->predictionService->getWeeks(2024);

        // If a week is selected in the request, fetch games for that week
        $games = $request->has('week') && $request->week
            ? $this->predictionService->getGamesByWeek($request->week, 2024)
            : [];

        // Return the view with weeks and games data
        return view('predict.index', compact('weeks', 'games'));
    }


    public function show($gameId)
    {
        $prediction = $this->predictionService->getPredictionDetails($gameId);

        if (isset($prediction['error'])) {
            return view('predict.game', ['error' => $prediction['error']]);
        }

        return view('predict.game', compact('prediction'));
    }

    public function sos()
    {
        function calculateSOS($teamId)
        {
            // Step 1: Fetch all games where the team played either home or away
            $games = DB::table('college_football_games')
                ->where('home_team_id', $teamId)
                ->orWhere('away_team_id', $teamId)
                ->get();

            // Step 2: Get opponent team IDs and their SP ratings
            $opponentRatings = [];
            foreach ($games as $game) {
                $opponentTeamId =
                    $game->home_team_id == $teamId
                        ? $game->away_team_id
                        : $game->home_team_id;

                $opponentRating = DB::table('college_football_sp_ratings')
                    ->where('team_id', $opponentTeamId)
                    ->value('rating');

                if ($opponentRating) {
                    $opponentRatings[] = $opponentRating;
                }
            }

            // Step 3: Calculate the average SP rating of the opponents (this is a basic SOS)
            $sos = collect($opponentRatings)->avg();

            return $sos;
        }

// Example usage:
        $teamId = 333; // replace with actual team ID
        $sos = calculateSOS($teamId);
        echo "Strength of Schedule (SOS) for team $teamId: $sos";

    }
}
