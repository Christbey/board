<div class="card shadow-md rounded-lg overflow-hidden mb-4">
    <div class="card-body p-4 bg-white">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Team</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">H2H</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Spread</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $score->homeTeam->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $score->home_team_score ?? '-' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \App\Helpers\FormatHelper::formatOdds($odd->h2h_home_price) }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \App\Helpers\FormatHelper::formatOdds($odd->spread_home_price) }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \App\Helpers\FormatHelper::formatOdds($odd->total_over_price) }}</td>
            </tr>
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $score->awayTeam->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $score->away_team_score ?? '-' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \App\Helpers\FormatHelper::formatOdds($odd->h2h_away_price) }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \App\Helpers\FormatHelper::formatOdds($odd->spread_away_price) }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \App\Helpers\FormatHelper::formatOdds($odd->total_under_price) }}</td>
            </tr>
            </tbody>
        </table>
        @if ($isCompleted)
            <div class="mt-4 text-center text-green-500">
                <p>Completed</p>
            </div>
        @endif
    </div>
</div>
