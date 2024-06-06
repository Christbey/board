<?php

namespace App\Livewire;


use Livewire\Component;
use Carbon\Carbon;

class DatePicker extends Component
{
public $sport;
public $date;

public function mount($sport)
{
$this->sport = $sport;
$this->date = Carbon::today()->format('Y-m-d');
}

public function updatedDate()
{
$this->filter();
}

public function filter()
{
$routeName = strtolower($this->sport) . '.odds';
return redirect()->route($routeName, ['date' => $this->date]);
}

public function render()
{
return view('livewire.date-picker');
}
}
