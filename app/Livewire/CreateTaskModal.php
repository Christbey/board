<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class CreateTaskModal extends Component
{
    public $task;
    public $completed = false;
    public $status = 'pending';
    public $priority = 'low';
    public $reminder_date;
    public $due_date;

    protected $rules = [
        'task' => 'required|string|max:255',
        'completed' => 'boolean',
        'status' => 'required|string',
        'priority' => 'required|string',
        'reminder_date' => 'nullable|date',
        'due_date' => 'nullable|date',
    ];

    public function save()
    {
        $this->validate();

        Task::create([
            'task' => $this->task,
            'completed' => $this->completed,
            'status' => $this->status,
            'priority' => $this->priority,
            'reminder_date' => $this->reminder_date,
            'due_date' => $this->due_date,
            'user_id' => Auth::id(),
        ]);

        $this->reset(['task', 'completed', 'status', 'priority', 'reminder_date', 'due_date']);
        $this->emit('taskAdded');
    }

    public function render()
    {
        return view('livewire.create-task-modal');
    }

    private function emit(string $string)
    {
    }
}