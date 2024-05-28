<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;

class CreateTaskRow extends Component
{
    public $task;

    public function mount(Task $task)
    {
        $this->task = $task;
    }

    public function render()
    {
        return view('livewire.create-task-row');
    }


}