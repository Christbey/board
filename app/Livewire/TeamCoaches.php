<?php

namespace App\Livewire;

use Livewire\Component;

class TeamCoaches extends Component
{
    public $coaches;

    public function mount($coaches)
    {
        $this->coaches = $coaches;
    }

    public function render()
    {
        return view('livewire.team-coaches');
    }
}
