<x-app-layout>
    @props(['tasks', 'filterDate'])

    <div {{ $attributes->merge(['class' => 'md:grid md:grid-cols-3 md:gap-6']) }} x-data="{ isOpen: false, activeTask: { id: '', task: '', completed: false, status: 'pending', priority: 'low', reminder_date: '', due_date: '' } }">
        <x-section-title>
            <x-slot name="title">Tasks</x-slot>
            <x-slot name="description">Manage your tasks efficiently</x-slot>
        </x-section-title>

        <!-- Create Task Modal -->
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
                        <livewire:task-row :task="$task" :key="$task->id" />
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Edit Task Modal -->
        <livewire:edit-task-modal />
    </div>

    <script>
        Livewire.on('taskAdded', () => {
            // Close the modal
            document.querySelector('[x-data]').__x.$data.isOpen = false;
        });

        Livewire.on('taskUpdated', () => {
            // Close the modal
            document.querySelector('[x-data]').__x.$data.isOpen = false;
        });
    </script>
</x-app-layout>
