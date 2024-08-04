<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\NflEspnTeam;

class TeamDetails extends Component
{
    public $team;

    public function mount($teamId)
    {
        $this->team = NflEspnTeam::findOrFail($teamId);
    }

    public function render()
    {
        return view('livewire.team-details');
    }
}
