<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;

class EditTaskModal extends Component
{
    public $task;
    public $task_name;
    public $completed;
    public $status;
    public $priority;
    public $reminder_date;
    public $due_date;

    protected $listeners = ['task-edit' => 'setTask'];

    protected $rules = [
        'task_name' => 'required|string|max:255',
        'completed' => 'boolean',
        'status' => 'required|string',
        'priority' => 'required|string',
        'reminder_date' => 'nullable|date',
        'due_date' => 'nullable|date',
    ];

    public function setTask($task)
    {
        $this->task = Task::find($task['id']);
        $this->task_name = $task['task'];
        $this->completed = $task['completed'];
        $this->status = $task['status'];
        $this->priority = $task['priority'];
        $this->reminder_date = $task['reminder_date'];
        $this->due_date = $task['due_date'];
    }

    public function save()
    {
        $this->validate();

        $this->task->update([
            'task' => $this->task_name,
            'completed' => $this->completed,
            'status' => $this->status,
            'priority' => $this->priority,
            'reminder_date' => $this->reminder_date,
            'due_date' => $this->due_date,
        ]);

        $this->emit('taskUpdated');
    }

    public function render()
    {
        return view('livewire.edit-task-modal');
    }

    private function emit(string $string)
    {
    }
}