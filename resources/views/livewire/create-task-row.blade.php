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