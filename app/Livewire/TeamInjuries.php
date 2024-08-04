<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\NflEspnInjury;

class TeamInjuries extends Component
{
    public $teamId;
    public $injuries;
    public $statusFilter = '';

    public function mount($teamId)
    {
        $this->teamId = $teamId;
        $this->loadInjuries();
    }

    public function updatedStatusFilter()
    {
        // This method is automatically called when the status filter is updated
    }

    public function applyFilter()
    {
        $this->loadInjuries();
    }

    public function loadInjuries()
    {
        $query = NflEspnInjury::with(['team', 'athlete'])->where('team_id', $this->teamId);

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $this->injuries = $query->get();
    }

    public function render()
    {
        return view('livewire.team-injuries', [
            'injuries' => $this->injuries
        ]);
    }
}
