<div x-data="{ isOpen: false }" @task-edit.window="isOpen = true">
    <div x-show="isOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Edit Task</h3>
                <form wire:submit.prevent="save">
                    <div class="mt-4">
                        <label for="task" class="block text-sm font-medium text-gray-700">Task</label>
                        <input type="text" wire:model="task_name" id="task" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        @error('task_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700">Completed</label>
                        <input type="checkbox" wire:model="completed" id="completed" class="mt-1">
                        @error('completed') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mt-4">
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select wire:model="status" id="status" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                        </select>
                        @error('status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mt-4">
                        <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
                        <select wire:model="priority" id="priority" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                        @error('priority') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mt-4">
                        <label for="reminder_date" class="block text-sm font-medium text-gray-700">Reminder Date</label>
                        <input type="datetime-local" wire:model="reminder_date" id="reminder_date" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        @error('reminder_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mt-4">
                        <label for="due_date" class="block text-sm font-medium text-gray-700">Due Date</label>
                        <input type="datetime-local" wire:model="due_date" id="due_date" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        @error('due_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Update Task</button>
                        <button type="button" @click="isOpen = false" class="px-4 py-2 bg-gray-500 text-white rounded">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>