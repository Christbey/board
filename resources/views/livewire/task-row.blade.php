<tr class="cursor-pointer hover:bg-gray-100" @click="$dispatch('task-edit', { task: {{ $task->toJson() }} })">
    <td class="px-6 py-4 whitespace-nowrap text-sm {{ $task->completed ? 'text-gray-500 line-through' : 'text-gray-900' }}">
        {{ $task->task }}
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
        {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('m/d/Y') : 'No Due Date' }}
    </td>

</tr>
