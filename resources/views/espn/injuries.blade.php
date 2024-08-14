<div>
    <div class="mb-4">
        <label for="statusFilter" class="block text-sm font-medium text-gray-700">Filter by Status:</label>
        <select wire:model="statusFilter" id="statusFilter"
                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
            <option value="">All Statuses</option>
            <option value="active">Active</option>
            <option value="injured">Injured</option>
            <!-- Add other statuses as needed -->
        </select>
    </div>

    @if($injuries->isEmpty())
        <p class="text-red-500">No injuries data available.</p>
    @else
        <h2 class="text-2xl font-semibold mb-2">Injuries Data:</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200">
                <thead>
                <tr>
                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Athlete Name
                    </th>
                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Team
                    </th>
                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Injury Type
                    </th>
                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Date
                    </th>
                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Description
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($injuries as $injury)
                    <tr class="bg-white">
                        <td class="py-2 px-4 border-b border-gray-200 text-sm">{{ $injury->athlete->full_name ?? 'Unknown' }}</td>
                        <td class="py-2 px-4 border-b border-gray-200 text-sm">{{ $injury->team->display_name ?? 'Unknown' }}</td>
                        <td class="py-2 px-4 border-b border-gray-200 text-sm">{{ $injury->type }}</td>
                        <td class="py-2 px-4 border-b border-gray-200 text-sm">{{ $injury->status }}</td>
                        <td class="py-2 px-4 border-b border-gray-200 text-sm">{{ $injury->date }}</td>
                        <td class="py-2 px-4 border-b border-gray-200 text-sm">{{ $injury->description }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <div class="mt-4">
            {{ $injuries->links() }} <!-- This will display the pagination links -->
        </div>
    @endif
</div>
