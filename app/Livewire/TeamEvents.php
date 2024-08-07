<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\NflEspnEvent;
use Carbon\Carbon;

class TeamEvents extends Component
{
    public $teamId;
    public $events;

    public function mount($teamId)
    {
        $this->teamId = $teamId;
        $currentYear = Carbon::now()->year;

        $this->events = NflEspnEvent::where(function ($query) use ($teamId) {
            $query->where('home_team_id', $teamId)
                ->orWhere('away_team_id', $teamId);
        })
            ->whereYear('date', $currentYear)
            ->get();
    }

    public function render()
    {
        return view('livewire.team-events');
    }
}
