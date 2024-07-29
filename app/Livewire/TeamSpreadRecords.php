<?php

namespace App\Livewire;

use Livewire\Component;

class TeamSpreadRecords extends Component
{
    public $records;

    public function mount($records)
    {
        $this->records = $records;
    }

    public function render()
    {
        return view('livewire.team-spread-records');
    }
}
