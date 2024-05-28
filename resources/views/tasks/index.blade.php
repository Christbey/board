<x-app-layout>
    @props(['tasks', 'filterDate'])

    <div {{ $attributes->merge(['class' => 'md:grid md:grid-cols-3 md:gap-6']) }} x-data="{ isOpen: false, activeTask: { id: '', task: '', completed: false, status: 'pending', priority: 'low', reminder_date: '', due_date: '' } }">
        <x-section-title>
            <x-slot name="title">Tasks</x-slot>
            <x-slot name="description">Manage your tasks efficiently</x-slot>
        </x-section-title>

        <livewire:create-task-modal />

        <div class="mt-5 md:mt-0 md:col-span-2">


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
                        <livewire:create-task-row :task="$task" :key="$task->id" />
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Edit Task Modal -->

        <div x-show="isOpen" @keydown.escape.window="isOpen = false" @click.away="isOpen = false" class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto px-4 sm:px-6" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 transform transition-all" x-on:click="showModal = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

                <div class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-ease-out duration-300 sm:w-full sm:mx-auto max-w-lg" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

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
        </div>
    </div>

</x-app-layout>