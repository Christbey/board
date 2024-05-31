<!-- resources/views/tasks/index.blade.php -->
<x-app-layout>
    @props(['tasks', 'filterDate'])

    <div>
        <div class="flex justify-between items-center mb-4">
            <div class="flex-1">
                <x-section-title>
                    <x-slot name="title">Tasks</x-slot>
                    <x-slot name="description">Manage your tasks efficiently</x-slot>
                </x-section-title>
            </div>

            <div class="flex-shrink-0">
                <!-- Create Task Modal -->
                <livewire:create-task-modal />
            </div>
        </div>

        <div class="flex justify-start mb-4">
            <form method="GET" action="{{ route('tasks.index') }}">
                <label>
                    <input type="date" name="filter_date" value="{{ $filterDate }}" class="p-2 border-gray-300 rounded-lg" onchange="this.form.submit()">
                </label>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow p-4 sm:p-6">
            <table class="min-w-full divide-y divide-gray-200 mt-4">
                <thead class="bg-gray-800">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Task</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Due Date</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($tasks as $task)
                    <livewire:task-row :task="$task" :key="$task->id" />
                @empty
                    <tr>
                        <td colspan="2" class="px-6 py-4 text-center text-gray-500">No available tasks.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <!-- Edit Task Modal -->
        <livewire:edit-task-modal />
    </div>
</x-app-layout>
