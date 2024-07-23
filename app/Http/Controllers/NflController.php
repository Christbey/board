<?php

namespace App\Http\Controllers;

use App\Models\NflTeam;
use App\Models\NflPrediction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class NflController extends Controller
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.oddsapi.key');
        $this->baseUrl = config('services.oddsapi.base_url');
    }

    public function index()
    {
        // Dispatch the command to log expected winning percentage and predicted scores
        Artisan::call('log:predicted-scores');

        $teams = NflTeam::all();

        // Calculate expected wins for each team
        $expectedWins = [];
        foreach ($teams as $team) {
            $homeWins = NflPrediction::where('team_id_home', $team->id)
                ->whereColumn('home_pts_prediction', '>', 'away_pts_prediction')
                ->count();

            $awayWins = NflPrediction::where('team_id_away', $team->id)
                ->whereColumn('away_pts_prediction', '>', 'home_pts_prediction')
                ->count();

            // Calculate the expected wins based on predicted points
            $expectedWins[$team->id] = $homeWins + $awayWins;

            // Alternatively, use the same logic from the show method if it involves more details
            $homePredictions = NflPrediction::where('team_id_home', $team->id)->get();
            $awayPredictions = NflPrediction::where('team_id_away', $team->id)->get();

            $expectedWins[$team->id] = $homePredictions->sum('home_win_percentage') / 100 +
                $awayPredictions->sum('away_win_percentage') / 100;
        }

        Log::info('Expected Wins:', $expectedWins);

        return view('nfl.teams', compact('teams', 'expectedWins'));
    }

    public function show($teamId)
    {
        // Dispatch the command to log expected winning percentage and predicted scores
        Artisan::call('log:predicted-scores');

        Log::info('Showing team with ID:', ['teamId' => $teamId]);

        $team = NflTeam::findOrFail($teamId);
        Log::info('Team:', ['team' => $team]);

        $homePredictions = NflPrediction::where('team_id_home', $team->id)->get();
        $awayPredictions = NflPrediction::where('team_id_away', $team->id)->get();

        $expectedWins = $homePredictions->sum('home_win_percentage') / 100 +
            $awayPredictions->sum('away_win_percentage') / 100;
        Log::info('Expected Wins:', ['expectedWins' => $expectedWins]);

        return view('nfl.show', compact('team', 'expectedWins'));
    }
}
