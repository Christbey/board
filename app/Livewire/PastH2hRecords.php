<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\EspnNflPastH2h;

class PastH2hRecords extends Component
{
    public $homeTeamId;
    public $awayTeamId;
    public $records = [];

    public function mount($homeTeamId, $awayTeamId)
    {
        $this->homeTeamId = $homeTeamId;
        $this->awayTeamId = $awayTeamId;
        $this->fetchRecords();
    }

    public function fetchRecords()
    {
        $this->records = EspnNflPastH2h::where(function ($query) {
            $query->where('home_team_id', $this->homeTeamId)
                ->where('away_team_id', $this->awayTeamId);
        })->orWhere(function ($query) {
            $query->where('home_team_id', $this->awayTeamId)
                ->where('away_team_id', $this->homeTeamId);
        })->get();
    }

    public function render()
    {
        return view('livewire.past-h2h-records');
    }
}
