<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;

// Add this import
use App\Models\NflEspnInjury;

class TeamInjuries extends Component
{
    use WithPagination;

    // Include the pagination trait

    public $teamId;
    public $statusFilter = '';

    public function mount($teamId)
    {
        $this->teamId = $teamId;
    }

    public function updatedStatusFilter()
    {
        $this->resetPage(); // Reset to the first page when the filter is updated
    }

    public function applyFilter()
    {
        $this->resetPage(); // Reset to the first page when applying a filter
    }

    public function loadInjuries()
    {
        $query = NflEspnInjury::with(['team', 'athlete'])->where('team_id', $this->teamId);

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        return $query->paginate(10); // Use pagination here
    }

    public function render()
    {
        return view('livewire.team-injuries', [
            'injuries' => $this->loadInjuries() // Pass the paginated data
        ]);
    }
}
