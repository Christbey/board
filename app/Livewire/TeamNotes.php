<?php

namespace App\Livewire;

use Livewire\Component;

class TeamNotes extends Component
{
    public $notes;

    public function mount($notes)
    {
        $this->notes = $notes;
    }

    public function render()
    {
        return view('livewire.team-notes');
    }
}
