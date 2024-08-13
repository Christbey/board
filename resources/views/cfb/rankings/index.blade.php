<x-app-layout>
    <div class="container mx-auto p-6">
        <h2 class="text-xl font-semibold mb-4">College Football Rankings</h2>

        <!-- Filter by Poll -->
        <form method="GET" action="{{ route('cfb.rankings.index') }}" class="mb-6">
            <div class="flex items-center space-x-4">
                <div>
                    <label for="poll" class="block text-sm font-medium text-gray-700">Select Poll:</label>
                    <select name="poll" id="poll"
                            class="block w-64 px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            onchange="this.form.submit()">
                        <option value="">All Polls</option>
                        @foreach($polls as $poll)
                            <option value="{{ $poll }}" {{ $selectedPoll == $poll ? 'selected' : '' }}>
                                {{ $poll }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>

        <div class="overflow-x-auto bg-white rounded-lg shadow-md">
            <table class="min-w-full bg-white">
                <thead>
                <tr class="bg-gray-100 border-b">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ranking
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Team</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Points
                    </th>
                </tr>
                </thead>
                <tbody>
                @forelse($rankings as $ranking)
                    <tr class="cursor-pointer hover:bg-gray-100"
                        onclick="window.location='{{ route('cfb.teams.show', $ranking->team->id) }}'">
                        <td class="px-6 py-4 whitespace-nowrap">{{ $ranking->rank }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $ranking->team->school ?? 'Unknown Team' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $ranking->points }}</td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">No rankings available.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
