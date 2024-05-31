<?php

namespace App\Livewire;

use Carbon\Carbon;
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
        'status' => 'required|string',
        'priority' => 'required|string',
        'reminder_date' => 'nullable|date',
        'due_date' => 'nullable|date',
    ];

    public function setTask($task): void
    {
        $this->task = Task::find($task['id']);

        if (!$this->task) {
            return;
        }

        $this->task_name = $this->task->task;
        $this->completed = $this->task->completed;
        $this->status = $this->task->status;
        $this->priority = $this->task->priority;
        $this->reminder_date = $this->task->reminder_date ? Carbon::parse($this->task->reminder_date)->format('Y-m-d\TH:i') : null;
        $this->due_date = $this->task->due_date ? Carbon::parse($this->task->due_date)->format('Y-m-d\TH:i') : null;

        // Open the modal
        $this->dispatchBrowserEvent('open-modal');
    }

    public function save()
    {
        $this->validate();

        $completed = $this->status === 'completed' ? 1 : 0;

        $this->task->update([
            'task' => $this->task_name,
            'completed' => $completed,
            'status' => $this->status,
            'priority' => $this->priority,
            'reminder_date' => $this->reminder_date,
            'due_date' => $this->due_date,
        ]);

        session()->flash('status', 'Task updated successfully!');
        return redirect()->route('tasks.index');
    }

    public function deleteTask()
    {
        if ($this->task) {
            $this->task->delete();

            session()->flash('status', 'Task deleted successfully!');
            return redirect()->route('tasks.index');
        }
    }

    public function render()
    {
        return view('livewire.edit-task-modal');
    }

    private function dispatchBrowserEvent(string $string)
    {
    }
}
