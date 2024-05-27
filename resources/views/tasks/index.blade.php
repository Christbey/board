<x-app-layout>
    @props(['tasks', 'actions', 'filterDate'])

    <div {{ $attributes->merge(['class' => 'md:grid md:grid-cols-3 md:gap-6']) }} x-data="{ isOpen: false, activeTask: { id: '', task: '', completed: '', status: '', priority: '', reminder_date: '', due_date: '' } }">
        <x-section-title>
            <x-slot name="title">Task</x-slot>
            <x-slot name="description">Some Stuff</x-slot>
        </x-section-title>

        <div class="mt-5 md:mt-0 md:col-span-2">
            <!-- Create Task Section -->
            <div class="px-4 py-3 mb-9 bg-white rounded-lg shadow sm:p-6">
                <h1 class="text-xl font-bold text-gray-700">Create Task</h1>
                <form action="{{ route('tasks.store') }}" method="POST" class="space-y-4 mb-6">
                    @csrf
                    <div class="flex flex-col">
                        <label for="task" class="text-gray-600">Task</label>
                        <input type="text" name="task" id="task" class="rounded border border-gray-300 focus:border-blue-500 focus:outline-none" placeholder="Add a new task">
                    </div>

                    <div class="flex items-center space-x-2">
                        <input type="checkbox" name="completed" id="completed" class="form-checkbox h-4 w-4 text-green-600 border-gray-300 rounded focus:ring focus:ring-green-500 focus:ring-opacity-50">
                        <label for="completed" class="text-sm font-medium text-gray-700">Completed?</label>
                    </div>

                    <div class="flex flex-col">
                        <label for="status" class="text-gray-600">Status</label>
                        <select name="status" id="status" class="rounded border border-gray-300 focus:border-blue-500 focus:outline-none">
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>

                    <div class="flex flex-col">
                        <label for="priority" class="text-gray-600">Priority</label>
                        <select name="priority" id="priority" class="rounded border border-gray-300 focus:border-blue-500 focus:outline-none">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>

                    <div class="flex flex-col">
                        <label for="reminder_date" class="text-gray-600">Reminder Date</label>
                        <input type="datetime-local" name="reminder_date" id="reminder_date" value="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}" class="rounded border border-gray-300 focus:border-blue-500 focus:outline-none">
                    </div>

                    <div class="flex flex-col">
                        <label for="due_date" class="text-gray-600">Due Date</label>
                        <input type="datetime-local" name="due_date" id="due_date" value="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}" class="rounded border border-gray-300 focus:border-blue-500 focus:outline-none">
                    </div>

                    <button type="submit" class="px-4 py-2 text-white bg-green-500 rounded hover:bg-green-600 focus:bg-green-600 focus:outline-none">Add</button>
                </form>
            </div>

            <!-- Filter and Task Table Section -->
            <div class="px-4 py-5 bg-white rounded-lg shadow sm:p-6 mb-4">
                <!-- Task Table -->
                <table class="min-w-full divide-y divide-gray-200 mt-4">
                    <thead class="bg-gray-800">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Task</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($tasks as $task)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm {{ $task->completed ? 'text-gray-500 line-through' : 'text-gray-900' }}">
                                {{ $task->task }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex space-x-4">
                                <!-- Edit Button -->
                                <button type="button"
                                        class="text-white bg-blue-500 hover:bg-blue-600 focus:bg-blue-600 rounded px-4 py-2 focus:outline-none"
                                        @click="isOpen = true; activeTask = { id: '{{ $task->id }}', task: '{{ $task->task }}', completed: '{{ $task->completed }}', status: '{{ $task->status }}', priority: '{{ $task->priority }}', reminder_date: '{{ $task->reminder_date }}', due_date: '{{ $task->due_date }}' }">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <!-- Delete Button -->
                                <form id="deleteForm_{{ $task->id }}" action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                            onclick="confirmDelete({{ $task->id }})"
                                            class="text-white bg-red-500 hover:bg-red-600 focus:bg-red-600 rounded px-4 py-2 focus:outline-none">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <script>
                            function confirmDelete(taskId) {
                                if (confirm('Are you sure you want to delete this task?')) {
                                    document.getElementById(`deleteForm_${taskId}`).submit();
                                }
                            }
                        </script>
                        <div x-show="isOpen" @keydown.escape.window="isOpen = false" @click.away="isOpen  = false" class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto px-4 sm:px-6" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                            <div class="bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all max-w-xl w-full mx-auto">
                                <div class="bg-gray-200 px-4 py-2 flex items-center justify-between">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Edit Task</h3>
                                    <button type="button" @click="isOpen  = false" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                                <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                    <div class="sm:flex sm:items-start">
                                        <div class="mt-3 w-full text-center sm:mt-0 sm:ml-4 sm:text-left">
                                            <div class="mt-2" @click.stop>
                                                <form :action="'{{ route('tasks.update', ['task' => '_id_']) }}'.replace('_id_', activeTask.id)" method="POST">
                                                    @csrf
                                                    @method('PATCH')

                                                    <!-- Task input -->
                                                    <input type="text" x-model="activeTask.task" name="task" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">

                                                    <!-- Completed checkbox -->
                                                    <label class="block mt-4 flex items-center space-x-2">
                                                        <input type="checkbox" name="completed" class="form-checkbox h-4 w-4 text-green-600 border-gray-300 rounded focus:ring focus:ring-green-500 focus:ring-opacity-50" x-model="activeTask.completed">
                                                        <span class="text-sm font-medium text-gray-700">Completed?</span>
                                                    </label>

                                                    <!-- Status select -->
                                                    <div class="mt-4">
                                                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                                        <select name="status" x-model="activeTask.status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                                            <option value="pending">Pending</option>
                                                            <option value="in_progress">In Progress</option>
                                                            <option value="completed">Completed</option>
                                                        </select>
                                                    </div>

                                                    <!-- Priority select -->
                                                    <div class="mt-4">
                                                        <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
                                                        <select name="priority" x-model="activeTask.priority" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                                            <option value="low">Low</option>
                                                            <option value="medium">Medium</option>
                                                            <option value="high">High</option>
                                                        </select>
                                                    </div>

                                                    <!-- Reminder Date input -->
                                                    <div class="mt-4">
                                                        <label for="reminder_date" class="block text-sm font-medium text-gray-700">Reminder Date</label>
                                                        <input type="datetime-local" name="reminder_date" x-model="activeTask.reminder_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                                    </div>

                                                    <!-- Due Date input -->
                                                    <div class="mt-4">
                                                        <label for="due_date" class="block text-sm font-medium text-gray-700">Due Date</label>
                                                        <input type="datetime-local" name="due_date" x-model="activeTask.due_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                                    </div>

                                                    <!-- Action buttons -->
                                                    <div class="flex mt-3 items-center justify-end space-x-2 py-2 text-end">
                                                        <!-- Save Changes button -->
                                                        <button type="submit" class="inline-flex justify-center py-2 px-3 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Include Alpine.js -->
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
