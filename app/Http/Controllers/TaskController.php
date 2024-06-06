<?php

namespace App\Http\Controllers;

use App\Events\TaskCreated;
use App\Events\TaskUpdated;
use App\Events\TaskDeleted;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    protected $validationRules = [
        'task' => 'required|string|max:255',
        'status' => 'required|string|in:pending,in_progress,completed',
        'priority' => 'required|string|in:low,medium,high',
        'reminder_date' => 'nullable|date',
        'due_date' => 'nullable|date',
    ];

    public function index(Request $request)
    {
        $filterDate = $request->input('filter_date', Carbon::now()->toDateString());

        $tasks = Task::whereBetween('created_at', [
            Carbon::parse($filterDate)->startOfDay(),
            Carbon::parse($filterDate)->endOfDay(),
        ])->get();

        return view('tasks.index', compact('tasks', 'filterDate'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->validationRules);
        $task = $this->createOrUpdateTask(new Task(), $validated);
        event(new TaskCreated($task));

        return redirect()->route('tasks.index')->with('status', 'Task created successfully!');
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate($this->validationRules);
        $task = $this->createOrUpdateTask($task, $validated);
        event(new TaskUpdated($task));

        return redirect()->route('tasks.index')->with('status', 'Task updated successfully!');
    }

    public function destroy(Task $task)
    {
        $task->delete();
        event(new TaskDeleted($task));

        return redirect()->route('tasks.index')->with('status', 'Task deleted successfully!');
    }

    private function createOrUpdateTask(Task $task, array $validated)
    {
        $task->fill([
            'task' => $validated['task'],
            'user_id' => Auth::id(),
            'is_completed' => $validated['status'] === 'completed',
            'status' => $validated['status'],
            'priority' => $validated['priority'],
            'reminder_date' => $validated['reminder_date'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
        ]);

        $task->save();

        return $task;
    }
}
