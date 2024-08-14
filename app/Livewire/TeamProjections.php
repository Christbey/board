<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\NflEspnTeamProjection;

class TeamProjections extends Component
{
    public $teamId;
    public $projections;
    public $leagueAverageWins;

    public function mount($teamId)
    {
        $this->teamId = $teamId;
        $this->loadProjections();
        $this->calculateLeagueAverageWins();
    }

    protected function loadProjections()
    {
        $this->projections = NflEspnTeamProjection::where('team_id', $this->teamId)->get();
    }

    protected function calculateLeagueAverageWins()
    {
        $this->leagueAverageWins = NflEspnTeamProjection::avg('projected_wins');
    }

    public function render()
    {
        return view('livewire.team-projections');
    }
}
