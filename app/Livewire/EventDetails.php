<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\NflEspnEvent;

class EventDetails extends Component
{
    public $eventId;
    public $event;

    public function mount($eventId)
    {
        $this->eventId = $eventId;
        $this->fetchEventDetails();
    }

    public function fetchEventDetails()
    {
        $this->event = NflEspnEvent::find($this->eventId);
    }

    public function render()
    {
        return view('livewire.event-details');
    }
}
