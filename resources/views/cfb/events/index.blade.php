@php use Carbon\Carbon; @endphp

<x-app-layout>
    <div class="container mx-auto p-6">
        <h2 class="text-xl font-semibold mb-4">College Football Games</h2>

        <!-- Filter by Conference and Week -->
        <form method="GET" action="{{ route('cfb.events.index') }}" class="mb-6">
            <div class="flex items-center space-x-4">
                <div>
                    <label for="conference" class="block text-sm font-medium text-gray-700">Select Conference:</label>
                    <select name="conference" id="conference"
                            class="block w-64 px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            onchange="this.form.submit()">
                        @foreach($conferences as $conference)
                            <option value="{{ $conference }}" {{ $selectedConference == $conference ? 'selected' : '' }}>
                                {{ $conference }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="week" class="block text-sm font-medium text-gray-700">Select Week:</label>
                    <select name="week" id="week"
                            class="block w-64 px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            onchange="this.form.submit()">
                        <option value="">All Weeks</option>
                        @foreach($weeks as $week)
                            <option value="{{ $week }}" {{ $selectedWeek == $week ? 'selected' : '' }}>
                                Week {{ $week }}
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Home
                        Team
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Away
                        Team
                    </th>

                </tr>
                </thead>
                <tbody>
                @forelse($games as $game)
                    <tr class="cursor-pointer hover:bg-gray-100"
                        onclick="window.location='{{ route('cfb.events.show', $game->id) }}'">
                        <td class="px-6 py-4 whitespace-nowrap">{{ Carbon::parse($game->start_date)->format('m/d/Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $game->homeTeam->school ?? 'Unknown Team' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $game->awayTeam->school ?? 'Unknown Team' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">No games available.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
