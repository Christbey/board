<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function __construct()
    {
    }

    public function index(Request $request)
    {
        $timezone = 'America/Chicago'; // CST
        $filterDate = $request->input('filter_date', Carbon::now($timezone)->toDateString());

        $tasks = Task::whereBetween('created_at', [
            Carbon::parse($filterDate, $timezone)->startOfDay(),
            Carbon::parse($filterDate, $timezone)->endOfDay(),
        ])->get();

        return view('tasks.index', compact('tasks', 'filterDate'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'task' => 'required|string|max:255',
            'completed' => 'sometimes|boolean',
            'status' => 'required|string',
            'priority' => 'required|string|in:low,medium,high',
            'reminder_date' => 'nullable|date',
            'due_date' => 'nullable|date',
        ]);

        Task::create([
            'task' => $validated['task'],
            'user_id' => Auth::id(),
            'completed' => $request->has('completed'),
            'status' => $validated['status'],
            'priority' => $validated['priority'],
            'reminder_date' => $validated['reminder_date'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return redirect()->route('tasks.index')->with('status', 'Task created successfully!');
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'task' => 'required|string|max:255',
            'completed' => 'sometimes|boolean',
            'status' => 'required|string',
            'priority' => 'required|string|in:low,medium,high',
            'reminder_date' => 'nullable|date',
            'due_date' => 'nullable|date',
        ]);

        $task->update([
            'task' => $validated['task'],
            'completed' => $request->has('completed'),
            'status' => $validated['status'],
            'priority' => $validated['priority'],
            'reminder_date' => $validated['reminder_date'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'updated_at' => Carbon::now(),
        ]);

        return redirect()->route('tasks.index')->with('status', 'Task updated successfully!');
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('tasks.index')->with('status', 'Task deleted successfully!');
    }
}
