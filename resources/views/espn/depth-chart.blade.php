<x-app-layout>
    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold mb-4">ESPN NFL Player Depth Chart</h1>

        <!-- Form to select the team_id -->
        <form method="GET" action="{{ route('espn.depth-chart') }}" class="mb-4">
            <label for="team_id" class="block text-sm font-medium text-gray-700">Select Team</label>
            <select id="team_id" name="team_id"
                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                @foreach($teams as $team)
                    <option value="{{ $team->team_id }}" {{ request('team_id') == $team->team_id ? 'selected' : '' }}>
                        {{ $team->display_name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="mt-2 px-4 py-2 bg-indigo-600 text-white rounded-md">Filter</button>
        </form>

        @if($depthChartData->isEmpty())
            <p class="text-red-500">No depth chart data available for the selected team.</p>
        @else
            <h2 class="text-2xl font-semibold mb-2">Depth Chart Data:</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead>
                    <tr>
                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Position
                        </th>
                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Athlete Name
                        </th>
                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Jersey
                        </th>
                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Depth
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($depthChartData as $entry)
                        <tr class="bg-white">
                            <td class="py-2 px-4 border-b border-gray-200 text-sm">{{ $entry->position }}</td>
                            <td class="py-2 px-4 border-b border-gray-200 text-sm">{{ $entry->athlete->full_name ?? null }}</td>
                            <td class="py-2 px-4 border-b border-gray-200 text-sm">{{ $entry->athlete->jersey ?? null }}</td>
                            <td class="py-2 px-4 border-b border-gray-200 text-sm">{{ $entry->depth }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>
