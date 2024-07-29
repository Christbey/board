<?php

namespace App\Livewire;

use Livewire\Component;

class TeamEvents extends Component
{
    public $events;

    public function mount($events)
    {
        $this->events = $events;
    }

    public function render()
    {
        return view('livewire.team-events');
    }
}
