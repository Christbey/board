<?php

namespace App\Livewire;

use Livewire\Component;
use App\Helpers\FormatHelper;

class MlbScoreOddsCard extends Component
{
    public $score;
    public $odd;

    public function mount($score, $odd)
    {
        $this->score = $score;
        $this->odd = $odd;
    }

    public function render()
    {
        return view('livewire.mlb-score-odds-card');
    }
}