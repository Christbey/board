<div >
    <!-- Button to Open Modal -->
    <div class="mb-4">
        <button wire:click="openCreateTaskModal" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Create Task</button>
    </div>

    <!-- Modal for Creating Task -->
    <x-modal id="createTaskModal" maxWidth="lg" wire:model="showModal">
        <x-slot name="title">Create Task</x-slot>
        <livewire:create-task-form />
    </x-modal>
</div>