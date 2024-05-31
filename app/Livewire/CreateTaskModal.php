<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CreateTaskModal extends Component
{
    public $task;
    public $status = 'pending';
    public $priority = 'low';
    public $reminder_date;
    public $due_date;

    protected $rules = [
        'task' => 'required|string|max:255',
        'status' => 'required|string',
        'priority' => 'required|string',
        'reminder_date' => 'nullable|date',
        'due_date' => 'nullable|date',
    ];

    public function mount()
    {
        $this->reminder_date = Carbon::now()->format('Y-m-d\TH:i');
        $this->due_date = Carbon::now()->format('Y-m-d\TH:i');
    }

    public function save()
    {
        $this->validate();

        $is_completed = $this->status === 'completed' ? 1 : 0;

        Task::create([
            'task' => $this->task,
            'is_completed' => $is_completed,
            'status' => $this->status,
            'priority' => $this->priority,
            'reminder_date' => $this->reminder_date,
            'due_date' => $this->due_date,
            'user_id' => Auth::id(),
        ]);

        session()->flash('status', 'Task created successfully!');
        return redirect()->route('tasks.index');
    }

    public function render()
    {
        return view('livewire.create-task-modal');
    }
}
