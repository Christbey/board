<?php

namespace App\Livewire;

use Livewire\Component;
use Carbon\Carbon;

class DatePicker extends Component
{
    public $sport;
    public $selectedDate;

    public function mount($selectedDate, $sport)
    {
        $this->sport = $sport;
        $this->selectedDate = $selectedDate;
    }

    public function updatedSelectedDate()
    {
        $this->filter();
    }

    public function filter()
    {
        $routeName = strtolower($this->sport) . '.event';
        return redirect()->route($routeName, ['selectedDate' => $this->selectedDate]);
    }

    public function render()
    {
        return view('livewire.date-picker');
    }
}
