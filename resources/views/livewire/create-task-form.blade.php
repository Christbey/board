<form wire:submit.prevent="submit" class="space-y-4 mb-6">
    @csrf
    @if ($method != 'POST')
        @method($method)
    @endif

    <div class="flex flex-col">
        <label for="task" class="text-gray-600">Task</label>
        <input type="text" wire:model="task" id="task" class="rounded border border-gray-300 focus:border-blue-500 focus:outline-none" placeholder="Add a new task">
    </div>

    <div class="flex items-center space-x-2">
        <input type="checkbox" wire:model="completed" id="completed" class="form-checkbox h-4 w-4 text-green-600 border-gray-300 rounded focus:ring focus:ring-green-500 focus:ring-opacity-50">
        <label for="completed" class="text-sm font-medium text-gray-700">Completed?</label>
    </div>

    <div class="flex flex-col">
        <label for="status" class="text-gray-600">Status</label>
        <select wire:model="status" id="status" class="rounded border border-gray-300 focus:border-blue-500 focus:outline-none">
            <option value="pending">Pending</option>
            <option value="in_progress">In Progress</option>
            <option value="completed">Completed</option>
        </select>
    </div>

    <div class="flex flex-col">
        <label for="priority" class="text-gray-600">Priority</label>
        <select wire:model="priority" id="priority" class="rounded border border-gray-300 focus:border-blue-500 focus:outline-none">
            <option value="low">Low</option>
            <option value="medium">Medium</option>
            <option value="high">High</option>
        </select>
    </div>

    <div class="flex flex-col">
        <label for="reminder_date" class="text-gray-600">Reminder Date</label>
        <input type="datetime-local" wire:model="reminder_date" id="reminder_date" class="rounded border border-gray-300 focus:border-blue-500 focus:outline-none">
    </div>

    <div class="flex flex-col">
        <label for="due_date" class="text-gray-600">Due Date</label>
        <input type="datetime-local" wire:model="due_date" id="due_date" class="rounded border border-gray-300 focus:border-blue-500 focus:outline-none">
    </div>

    <button type="submit" class=“px-4 py-2 text-white bg-green-500 rounded hover:bg-green-600 focus:bg-green-600 focus:outline-none”>{{ $buttonText }}</button>
</form>