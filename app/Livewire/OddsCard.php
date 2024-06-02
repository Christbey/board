<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Log;

class OddsCard extends Component
{
    public $odd;
    public $sport;

    public function mount($odd, $sport)
    {
        if (!is_object($odd) || !method_exists($odd, 'getAttributes')) {
            Log::error('Odd is not a valid object.', ['odd' => $odd]);
            return;
        }

        $this->odd = $odd;
        $this->sport = $sport;
    }

    public function render()
    {
        $spread_away = $this->odd->spread_away_point ?? 'N/A';
        $spread_home = $this->odd->spread_home_point ?? 'N/A';
        $total_over = $this->odd->total_over_point ?? 'N/A';
        $total_under = $this->odd->total_under_point ?? 'N/A';
        $moneyline_away = $this->odd->h2h_away_price ?? 'N/A';
        $moneyline_home = $this->odd->h2h_home_price ?? 'N/A';

        return view('livewire.odds-card', [
            'spread_away' => $spread_away,
            'spread_home' => $spread_home,
            'total_over' => $total_over,
            'total_under' => $total_under,
            'moneyline_away' => $moneyline_away,
            'moneyline_home' => $moneyline_home,
        ]);
    }
}
