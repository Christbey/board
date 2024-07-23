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
        $expectedWins = $this->calculateExpectedWinsForAllTeams($teams);

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

    private function calculateExpectedWins($teamId)
    {
        $homePredictions = NflPrediction::where('team_id_home', $teamId)->get();
        $awayPredictions = NflPrediction::where('team_id_away', $teamId)->get();

        $expectedWins = $homePredictions->sum('home_win_percentage') / 100 +
            $awayPredictions->sum('away_win_percentage') / 100;

        return $expectedWins;
    }

    private function calculateExpectedWinsForAllTeams($teams)
    {
        $expectedWins = [];

        foreach ($teams as $team) {
            $expectedWins[$team->id] = $this->calculateExpectedWins($team->id);
        }

        return $expectedWins;
    }
}
