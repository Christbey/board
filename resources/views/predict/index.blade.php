@php use Carbon\Carbon; @endphp
<x-app-layout>
    <div class="max-w-2xl mx-auto mt-8">
        <!-- Week Selection Form -->
        <form method="GET" action="{{ route('predict.index') }}" class="mb-6">
            <div class="mb-4">
                <label for="week" class="block text-gray-700 font-medium mb-2">Select Week:</label>
                <select name="week" id="week" onchange="this.form.submit()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring focus:ring-indigo-200">
                    <option value="">-- Select Week --</option>
                    @foreach($weeks as $week)
                        <option value="{{ $week->week }}" {{ request('week') == $week->week ? 'selected' : '' }}>
                            Week {{ $week->week }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
        <form method="GET" id="gameForm" class="mb-6">
            <div class="mb-4">
                <label for="game_id" class="block text-gray-700 font-medium mb-2">Select Game:</label>
                <select name="game_id" id="game_id" onchange="submitGameForm(this.value)"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring focus:ring-indigo-200">
                    <option value="">-- Select Game --</option>
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
            <table class="min-w-full bg-white border border-gray-300 rounded-lg mb-6">
                <thead>
                <tr>
                    <th class="px-4 py-2 border-b">Home Team</th>
                    <th class="px-4 py-2 border-b">Away Team</th>
                    <th class="px-4 py-2 border-b">Date</th>
                </tr>
                </thead>
                <tbody>
                @foreach($games as $game)
                    <tr>
                        <td class="px-4 py-2 border-b">{{ $game->home_team }}</td>
                        <td class="px-4 py-2 border-b">{{ $game->away_team }}</td>
                        <td class="px-4 py-2 border-b">
                            {{ Carbon::parse($game->start_date)->format('n/j ga') }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <!-- Game Selection Form -->

        @else
            @if(request('week'))
                <p class="text-center text-gray-600 mb-6">No games available for this week.</p>
            @endif
        @endif
    </div>

    <script>
        function submitGameForm(gameId) {
            if (gameId) {
                var url = '{{ route("predict.game", ":gameId") }}';
                url = url.replace(':gameId', gameId);
                document.getElementById('gameForm').action = url;
                document.getElementById('gameForm').submit();
            }
        }
    </script>
</x-app-layout>
