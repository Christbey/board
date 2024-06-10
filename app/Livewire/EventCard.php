<?php

namespace App\Livewire;

use Livewire\Component;
use App\Helpers\FormatHelper;

class EventCard extends Component
{
    public $score;
    public $odd;
    public $winnerColor;
    public $isCompleted;

    public function mount($score, $odd, $winnerColor)
    {
        $this->score = $score;
        $this->odd = $odd;
        $this->winnerColor = $winnerColor;
        $this->isCompleted = $this->score->completed;
    }

    public function render()
    {
        return view('livewire.event-card');
    }
}
