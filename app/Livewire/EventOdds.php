<?php

namespace App\Livewire;

use App\Models\NflEspnEventOdd;
use Livewire\Component;

class EventOdds extends Component
{
    public $eventId;
    public $odds = [];

    public function mount($eventId)
    {
        $this->eventId = $eventId;
        $this->fetchOdds();
    }

    public function fetchOdds()
    {
        $this->odds = NflEspnEventOdd::where('event_id', $this->eventId)->get();
    }

    public function render()
    {
        return view('livewire.event-odds');
    }
}
