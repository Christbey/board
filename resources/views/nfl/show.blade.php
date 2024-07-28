@php
    use App\Helpers\FormatHelper;
@endphp

<x-app-layout>
    <div class="container mx-auto p-4 sm:p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold">{{ $team->name }}</h1>
            <a href="{{ url()->previous() }}" class="text-blue-600 hover:text-blue-800">
                ← Back
            </a>
        </div>
        <div class="bg-white shadow-md rounded-lg p-4 sm:p-6">
            <p class="text-lg mb-4"><strong>Stadium:</strong> {{ $team->stadium }}</p>
            <p class="text-lg mb-4"><strong>Expected Wins:</strong> {{ number_format($expectedWins, 2) }}</p>
            <p class="text-lg mb-4"><strong>Current Season Record:</strong> {{ $currentSeasonRecord['wins'] }}
                -{{ $currentSeasonRecord['losses'] }}</p>
            <p class="text-lg mb-4"><strong>Previous Season Record:</strong> {{ $previousSeasonRecord['wins'] }}
                -{{ $previousSeasonRecord['losses'] }}</p>
        </div>

        <div class="bg-white shadow-md rounded-lg p-4 sm:p-6 mt-6">
            <h2 class="text-xl font-bold mb-4">Predicted Outcomes</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 sm:px-6 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Game Date
                        </th>
                        <th class="px-3 py-2 sm:px-6 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Opponent
                        </th>
                        <th class="px-3 py-2 sm:px-6 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Predicted Home Points
                        </th>
                        <th class="px-3 py-2 sm:px-6 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Predicted Away Points
                        </th>
                        <th class="px-3 py-2 sm:px-6 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Home Win Percentage
                        </th>
                        <th class="px-3 py-2 sm:px-6 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Away Win Percentage
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($predictions as $prediction)
                        <tr>
                            <td class="px-3 py-2 sm:px-6 sm:py-4 whitespace-nowrap">{{ $prediction->game_date }}</td>
                            <td class="px-3 py-2 sm:px-6 sm:py-4 whitespace-nowrap">{{ FormatHelper::formatOpponent($prediction, $team->id) }}</td>
                            <td class="px-3 py-2 sm:px-6 sm:py-4 whitespace-nowrap">{{ $prediction->home_pts_prediction }}</td>
                            <td class="px-3 py-2 sm:px-6 sm:py-4 whitespace-nowrap">{{ $prediction->away_pts_prediction }}</td>
                            <td class="px-3 py-2 sm:px-6 sm:py-4 whitespace-nowrap">{{ number_format($prediction->home_win_percentage, 2) }}
                                %
                            </td>
                            <td class="px-3 py-2 sm:px-6 sm:py-4 whitespace-nowrap">{{ number_format($prediction->away_win_percentage, 2) }}
                                %
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white shadow-md rounded-lg p-4 sm:p-6 mt-6">
            <h2 class="text-xl font-bold mb-4">Players</h2>

            <!-- Filter Form -->
            <form method="GET" action="{{ route('nfl.show', ['team' => $team->id]) }}" class="mb-4">
                <div class="flex items-center">
                    <label for="position" class="mr-2 text-sm font-medium text-gray-700">Filter by position:</label>
                    <select name="position" id="position"
                            class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All</option>
                        @foreach ($positions as $pos)
                            <option value="{{ $pos }}"{{ $position == $pos ? ' selected' : '' }}>{{ $pos }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="ml-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Filter
                    </button>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 sm:px-6 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Player Name
                        </th>
                        <th class="px-3 py-2 sm:px-6 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Position
                        </th>
                        <th class="px-3 py-2 sm:px-6 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Number
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($players as $player)
                        <tr>
                            <td class="px-3 py-2 sm:px-6 sm:py-4 whitespace-nowrap">{{ $player->longName }}</td>
                            <td class="px-3 py-2 sm:px-6 sm:py-4 whitespace-nowrap">{{ $player->pos }}</td>
                            <td class="px-3 py-2 sm:px-6 sm:py-4 whitespace-nowrap">{{ $player->jerseyNum }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
