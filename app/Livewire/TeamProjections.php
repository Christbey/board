<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\NflEspnTeamProjection;

class TeamProjections extends Component
{
    public $teamId;
    public $projections;

    public function mount($teamId)
    {
        $this->teamId = $teamId;
        $this->projections = NflEspnTeamProjection::where('team_id', $this->teamId)->get();
    }

    public function render()
    {
        return view('livewire.team-projections');
    }
}
