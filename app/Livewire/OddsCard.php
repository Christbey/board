<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Log;

class OddsCard extends Component
{
    public $odd;

    public function mount($odd)
    {
        if (!is_array($odd)) {
            Log::error('Odd is not an array.', ['odd' => $odd]);
            return;
        }
        $this->odd = $odd;
    }

    public function render()
    {
        $spread_away = 'N/A';
        $spread_home = 'N/A';
        $total_over = 'N/A';
        $total_under = 'N/A';
        $moneyline_away = 'N/A';
        $moneyline_home = 'N/A';

        if (isset($this->odd['bookmakers'][0]['markets']) && is_array($this->odd['bookmakers'][0]['markets'])) {
            foreach ($this->odd['bookmakers'][0]['markets'] as $market) {
                if (!is_array($market)) {
                    Log::error('Market is not an array.', ['market' => $market]);
                    continue;
                }
                foreach ($market['outcomes'] as $outcome) {
                    if (!is_array($outcome)) {
                        Log::error('Outcome is not an array.', ['outcome' => $outcome]);
                        continue;
                    }

                    if ($market['key'] == 'spreads') {
                        if ($outcome['name'] == $this->odd['away_team']) {
                            $spread_away = $outcome['point'] ?? 'N/A';
                        } elseif ($outcome['name'] == $this->odd['home_team']) {
                            $spread_home = $outcome['point'] ?? 'N/A';
                        }
                    } elseif ($market['key'] == 'totals') {
                        if ($outcome['name'] == 'Over') {
                            $total_over = $outcome['point'] ?? 'N/A';
                        } elseif ($outcome['name'] == 'Under') {
                            $total_under = $outcome['point'] ?? 'N/A';
                        }
                    } elseif ($market['key'] == 'h2h') {
                        if ($outcome['name'] == $this->odd['away_team']) {
                            $moneyline_away = $outcome['price'] ?? 'N/A';
                        } elseif ($outcome['name'] == $this->odd['home_team']) {
                            $moneyline_home = $outcome['price'] ?? 'N/A';
                        }
                    }
                }
            }
        }

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
