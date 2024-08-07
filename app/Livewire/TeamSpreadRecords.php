<?php

namespace App\Livewire;

use App\Models\NflEspnAtsRecord;
use Livewire\Component;

// Adjust the model name if necessary

class TeamSpreadRecords extends Component
{
    public $teamId;
    public $records;

    public function mount($teamId)
    {
        $this->teamId = $teamId;
        $this->fetchRecords();
    }

    public function fetchRecords()
    {
        $this->records = NflEspnAtsRecord::where('team_id', $this->teamId)->get();
    }

    public function render()
    {
        return view('livewire.team-spread-records');
    }
}
