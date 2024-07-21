<?php

namespace App\Http\Controllers;

use App\Models\NflTeam;
use App\Models\NflTeamSchedule;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\Elo\EloRatingSystem;

class NflController extends Controller
{
    protected mixed $apiKey;
    protected mixed $baseUrl;
    protected EloRatingSystem $eloRatingSystem;

    public function __construct(EloRatingSystem $eloRatingSystem)
    {
        $this->apiKey = config('services.oddsapi.key');
        $this->baseUrl = config('services.oddsapi.base_url');
        $this->eloRatingSystem = $eloRatingSystem;
    }

    public function index()
    {
        $teams = NflTeam::all();
        $expectedWins = $this->eloRatingSystem->calculateExpectedWinsForTeams();

        // Fetch next three upcoming games for each team
        $nextOpponents = [];
        foreach ($teams as $team) {
            $nextOpponents[$team->id] = NflTeamSchedule::where(function ($query) use ($team) {
                $query->where('team_id_home', $team->id)
                    ->orWhere('team_id_away', $team->id);
            })
                ->where('game_date', '>', '2024-03-01')
                ->whereNull('home_result')
                ->whereNull('away_result')
                ->orderBy('game_date')
                ->take(3)
                ->get(['game_date', 'home', 'away', 'team_id_home', 'team_id_away']);
        }

        return view('nfl.teams', compact('teams', 'expectedWins', 'nextOpponents'));
    }
}
