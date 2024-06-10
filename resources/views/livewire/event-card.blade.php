<div class="card shadow-md rounded-lg overflow-hidden mb-4">
    <div class="card-body bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Team</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Spread</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">H2H</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $score->homeTeam->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">{{ $score->home_team_score ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">{{ \App\Helpers\FormatHelper::formatOdds($odd->spread_home_point) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">{{ \App\Helpers\FormatHelper::formatOdds($odd->total_over_point) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">{{ \App\Helpers\FormatHelper::formatOdds($odd->h2h_home_price) }}</td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $score->awayTeam->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">{{ $score->away_team_score ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">{{ \App\Helpers\FormatHelper::formatOdds($odd->spread_away_point) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">{{ \App\Helpers\FormatHelper::formatOdds($odd->total_under_point) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">{{ \App\Helpers\FormatHelper::formatOdds($odd->h2h_away_price) }}</td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="bg-gray-50 p-2 text-right">
            @if ($isCompleted)
                <div class="text-xs text-green-500">
                    <p>Completed</p>
                </div>
            @else
                <div class="text-red-500 text-xs">
                    <p>Commence Time: {{ \Carbon\Carbon::parse($score->commence_time)->format('Y-m-d H:i:s') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
