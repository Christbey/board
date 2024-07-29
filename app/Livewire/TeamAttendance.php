<?php

namespace App\Livewire;

use Livewire\Component;

class TeamAttendance extends Component
{
    public $attendance;

    public function mount($attendance)
    {
        $this->attendance = $attendance;
    }

    public function render()
    {
        return view('livewire.team-attendance');
    }
}
