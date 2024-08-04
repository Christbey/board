<div class="bg-white p-6 rounded-lg shadow mb-6">
    <h2 class="text-xl font-semibold mb-4">Injuries</h2>

    <div class="mb-4">
        <label for="statusFilter" class="block text-sm font-medium text-gray-700">Filter by Status</label>
        <select id="statusFilter" wire:model="statusFilter"
                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
            <option value="">All</option>
            <option value="Active">Active</option>
            <option value="Questionable">Questionable</option>
            <option value="Out">Out</option>
        </select>
        <button wire:click="applyFilter"
                class="mt-2 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
            Apply Filter
        </button>
    </div>

    @if($injuries && $injuries->isNotEmpty())
        <table class="min-w-full bg-white rounded-lg shadow">
            <thead>
            <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                <th class="py-3 px-6 text-left">Athlete</th>
                <th class="py-3 px-6 text-left">Status</th>
                <th class="py-3 px-6 text-left">Description</th>
                <th class="py-3 px-6 text-left">Date</th>
            </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
            @foreach($injuries as $injury)
                <tr class="border-b border-gray-200 hover:bg-gray-100">
                    <td class="py-3 px-6 text-left">{{ $injury->athlete->full_name }}</td>
                    <td class="py-3 px-6 text-left">{{ $injury->status }}</td>
                    <td class="py-3 px-6 text-left">{{ $injury->description }}</td>
                    <td class="py-3 px-6 text-left">{{ $injury->date }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        <p>No injuries reported for this team.</p>
    @endif
</div>
