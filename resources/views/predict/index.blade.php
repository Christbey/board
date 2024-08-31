@php use Carbon\Carbon; @endphp
<x-app-layout>
    <div class="flex items-center justify-center min-h-screen bg-gray-100 px-4 sm:px-6 lg:px-8">
        <div class="p-8 rounded-3xl bg-white max-w-2xl w-full shadow-xl">
            <!-- Week Selection Form -->
            <form method="GET" action="{{ route('predict.index') }}" class="mb-6">
                <div class="mb-4">
                    <label for="week" class="block text-sm font-medium text-gray-700 mb-2">Select Week:</label>
                    <select name="week" id="week" onchange="this.form.submit()"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Select Week --</option>
                        @foreach($weeks as $week)
                            <option value="{{ $week->week }}" {{ request('week') == $week->week ? 'selected' : '' }}>
                                Week {{ $week->week }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>

            <!-- Game Selection Form -->
            <form method="GET" id="gameForm" class="mb-6">
                <div class="mb-4">
                    <label for="game_id" class="block text-sm font-medium text-gray-700 mb-2">Select Game:</label>
                    <select name="game_id" id="game_id" onchange="submitGameForm(this.value)"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="" disabled>-- Select Game --</option>
                        @foreach($games as $game)
                            <option value="{{ $game->id }}">
                                {{ $game->home_team }} vs {{ $game->away_team }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>

            <!-- Games Table -->
            @if(request('week') && $games->count() > 0)
                <div class="overflow-x-auto bg-white rounded-lg shadow-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Home Team
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Away Team
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($games as $game)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">{{ $game->home_team }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $game->away_team }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ Carbon::parse($game->start_date)->format('n/j') }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                @if(request('week'))
                    <p class="text-center text-gray-600 mt-4">No games available for this week.</p>
                @endif
            @endif
        </div>
    </div>

    <script>
        function submitGameForm(gameId) {
            if (gameId) {
                const url = '{{ route("predict.game", ":gameId") }}'.replace(':gameId', gameId);
                document.getElementById('gameForm').action = url;
                document.getElementById('gameForm').submit();
            }
        }
    </script>
</x-app-layout>
