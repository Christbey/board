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

        // Initialize expectedWins array
        $this->expectedWins = array_fill_keys($this->teams->pluck('id')->toArray(), 0);

        // Retrieve predictions and calculate expected wins
        foreach ($this->teams as $team) {
            $homePredictions = NflPrediction::where('team_id_home', $team->id)->get();
            $awayPredictions = NflPrediction::where('team_id_away', $team->id)->get();

            $this->expectedWins[$team->id] = $homePredictions->sum('home_win_percentage') / 100 +
                $awayPredictions->sum('away_win_percentage') / 100;
        }

        Log::info('Expected Wins:', $this->expectedWins);
    }

    public function render()
    {
        return view('livewire.nfl-teams', [
            'teams' => $this->teams,
            'expectedWins' => $this->expectedWins
        ]);
    }
}
