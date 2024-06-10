<div>
    <form wire:submit.prevent="filter">
        <div class="mb-4">
            <input type="date" wire:model="selectedDate" class="border-gray-300 rounded px-3 py-2">
            <button type="submit" class="btn btn-primary">Filter</button>

        </div>
    </form>
</div>
