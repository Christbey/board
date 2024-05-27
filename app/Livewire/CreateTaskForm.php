<?php
// app/Http/Livewire/CreateTaskForm.php

namespace App\Livewire;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CreateTaskForm extends Component
{
    public $task;
    public $completed = false;
    public $status = 'pending';
    public $priority = 'low';
    public $reminder_date;
    public $due_date;
    public $action;
    public $method;
    public $buttonText;

    protected $rules = [
        'task' => 'required|string|max:255',
        'completed' => 'sometimes|boolean',
        'status' => 'required|string',
        'priority' => 'required|string|in:low,medium,high',
        'reminder_date' => 'nullable|date',
        'due_date' => 'nullable|date',
    ];

    public function mount($task = null, $action = '', $method = 'POST', $buttonText = 'Add')
    {
        if ($task) {
            $this->task = $task['task'];
            $this->completed = $task['completed'];
            $this->status = $task['status'];
            $this->priority = $task['priority'];
            $this->reminder_date = Carbon::parse($task['reminder_date'])->format('Y-m-d\TH:i');
            $this->due_date = Carbon::parse($task['due_date'])->format('Y-m-d\TH:i');
        } else {
            $this->reminder_date = Carbon::now()->format('Y-m-d\TH:i');
            $this->due_date = Carbon::now()->format('Y-m-d\TH:i');
        }

        $this->action = $action;
        $this->method = $method;
        $this->buttonText = $buttonText;
    }

    public function submit()
    {
        $validated = $this->validate();

        $task = Task::updateOrCreate(
            ['id' => $this->task['id'] ?? null],
            array_merge($validated, [
                'user_id' => Auth::id(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ])
        );

        session()->flash('status', 'Task ' . ($this->method == 'POST' ? 'created' : 'updated') . ' successfully!');

        return redirect()->route('tasks.index');
    }

    public function render()
    {
        return view('livewire.create-task-form');
    }
}