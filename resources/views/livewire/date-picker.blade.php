<div>
    <form wire:submit.prevent="filter">
        <div class="mb-4">
            <input type="date" wire:model="date" class="border rounded px-3 py-2">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Filter</button>
        </div>
    </form>
</div>
