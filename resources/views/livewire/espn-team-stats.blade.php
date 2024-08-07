<div class="bg-white p-6 rounded-lg shadow mb-6">
    <h2 class="text-xl font-semibold mb-4">Team Statistics</h2>

    <div class="mb-4">
        <select wire:model="season" class="p-2 border border-gray-300 rounded-lg w-full">
            <option value="">Select Season</option>
            @foreach($seasons as $season)
                <option value="{{ $season }}">{{ $season }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <select wire:model="category" class="p-2 border border-gray-300 rounded-lg w-full">
            <option value="">Select Category</option>
            @foreach($categories as $category)
                <option value="{{ $category }}">{{ $category }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <button wire:click="fetchStatistics"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Load Statistics
        </button>
    </div>

    @if($statistics && $statistics->isEmpty())
        <p class="text-gray-600">No statistics available for this team.</p>
    @elseif($statistics)
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-lg shadow">
                <thead>
                <tr class="bg-gray-800 text-white uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">Category</th>
                    <th class="py-3 px-6 text-left">Stat Name</th>
                    <th class="py-3 px-6 text-left">Stat Value</th>
                    <th class="py-3 px-6 text-left">Rank</th>
                </tr>
                </thead>
                <tbody class="text-gray-700 text-sm font-light">
                @foreach($statistics as $stat)
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left">{{ $stat->category }}</td>
                        <td class="py-3 px-6 text-left">{{ $stat->stat_name }}</td>
                        <td class="py-3 px-6 text-left">{{ $stat->stat_display_value }}</td>
                        <td class="py-3 px-6 text-left">{{ $stat->stat_rank_display_value }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
