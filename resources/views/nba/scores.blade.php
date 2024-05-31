<x-app-layout>
    <div class="max-w-6xl mx-auto py-6 sm:px-6 lg:px-8">
        @if (!empty($scores))
            <h1 class="text-2xl font-bold mb-6 text-center">NBA Scores</h1>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                    <tr>
                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Game</th>
                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Home Team</th>
                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Away Team</th>
                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Home Score</th>
                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Away Score</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($scores as $score)
                        <tr>
                            <td class="py-2 px-4 border-b border-gray-200">{{ $score['id'] ?? '' }}</td>
                            <td class="py-2 px-4 border-b border-gray-200">{{ $score['home_team'] ?? '' }}</td>
                            <td class="py-2 px-4 border-b border-gray-200">{{ $score['away_team'] ?? '' }}</td>
                            <td class="py-2 px-4 border-b border-gray-200">{{ $score['scores'][0]['score'] ?? '' }}</td>
                            <td class="py-2 px-4 border-b border-gray-200">{{ $score['scores'][1]['score'] ?? '' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-center text-gray-500">No NBA scores available.</p>
        @endif
    </div>
</x-app-layout>
