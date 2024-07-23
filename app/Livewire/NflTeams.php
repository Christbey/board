<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\NflTeam;
use App\Models\NflPrediction;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class NflTeams extends Component
{
    public $teams;
    public $expectedWins;

    public function mount()
    {
        // Dispatch the command to log expected winning percentage and predicted scores
        Artisan::call('log:predicted-scores');

        $this->teams = NflTeam::all();

        // Calculate expected wins for each team
        $this->expectedWins = $this->calculateExpectedWinsForAllTeams($this->teams);

        Log::info('Expected Wins:', $this->expectedWins);
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

    public function render()
    {
        return view('livewire.nfl-teams', [
            'teams' => $this->teams,
            'expectedWins' => $this->expectedWins
        ]);
    }
}
