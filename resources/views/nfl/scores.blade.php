<!-- resources/views/nfl_scores/index.blade.php -->
<x-app-layout>
    <div class="bg-white rounded-lg shadow p-4 sm:p-6">
        @isset($errorMessage)
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Error:</strong>
                <span class="block sm:inline">{{ $errorMessage }}</span>
            </div>
        @endisset
        <table class="min-w-full divide-y divide-gray-200 mt-4">
            <thead class="bg-gray-800">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Game ID</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Home Team</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Away Team</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Home Score</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Away Score</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($scores as $score)
                <tr>
                    <td class="px-6 py-4">{{ $score['id'] ?? '' }}</td>
                    <td class="px-6 py-4">{{ $score['home_team'] ?? 'N/A' }}</td>
                    <td class="px-6 py-4">{{ $score['away_team'] ?? 'N/A' }}</td>
                    <td class="px-6 py-4">{{ $score['home_score'] ?? 'N/A' }}</td>
                    <td class="px-6 py-4">{{ $score['away_score'] ?? 'N/A' }}</td>
                    <td class="px-6 py-4">{{ $score['status'] ?? 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No NFL scores available.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
