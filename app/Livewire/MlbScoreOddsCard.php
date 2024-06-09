<?php

namespace App\Livewire;

use Livewire\Component;
use App\Helpers\FormatHelper;

class MlbScoreOddsCard extends Component
{
    public $score;
    public $odd;
    public $isCompleted;

    public function mount($score, $odd)
    {
        $this->score = $score;
        $this->odd = $odd;
        $this->isCompleted = $this->score->completed;
    }

    public function render()
    {
        return view('livewire.mlb-score-odds-card');
    }
}
