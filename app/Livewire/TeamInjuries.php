<?php

namespace App\Livewire;

use Livewire\Component;

class TeamInjuries extends Component
{
    public $injuries;

    public function mount($injuries)
    {
        $this->injuries = $injuries;
    }

    public function render()
    {
        return view('livewire.team-injuries');
    }
}
