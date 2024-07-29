<?php

namespace App\Livewire;

use Livewire\Component;

class TeamDetails extends Component
{
    public $teamData;

    public function mount($teamData)
    {
        $this->teamData = $teamData;
    }

    public function render()
    {
        return view('livewire.team-details');
    }
}
