<?php

namespace App\Livewire;

use Livewire\Component;
use App\Helpers\FormatHelper;

class OddsCard extends Component
{
    public $odd;
    public $sport;

    public function render()
    {
        return view('livewire.odds-card', [
            'formattedOdds' => $this->formatOdds(),
        ]);
    }

    protected function formatOdds()
    {
        return [
            'h2h_home_price' => FormatHelper::formatOdds($this->odd->h2h_home_price),
            'h2h_away_price' => FormatHelper::formatOdds($this->odd->h2h_away_price),
            'spread_home_price' => FormatHelper::formatOdds($this->odd->spread_home_price),
            'spread_away_price' => FormatHelper::formatOdds($this->odd->spread_away_price),
            'total_over_price' => FormatHelper::formatOdds($this->odd->total_over_price),
            'total_under_price' => FormatHelper::formatOdds($this->odd->total_under_price),
        ];
    }
}
