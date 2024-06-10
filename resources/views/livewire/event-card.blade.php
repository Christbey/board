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
                @foreach (['home', 'away'] as $team)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $score->{$team . 'Team'}->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">{{ $score->{$team . '_team_score'} ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            @if (is_null($odd->{'spread_' . $team . '_point'}))
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-auto" viewBox="0 0 448 512"><!-- Font Awesome Free 6.5.2 by @fontawesome --><path fill="#6b7280" d="M144 144v48H304V144c0-44.2-35.8-80-80-80s-80 35.8-80 80zM80 192V144C80 64.5 144.5 0 224 0s144 64.5 144 144v48h16c35.3 0 64 28.7 64 64V448c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V256c0-35.3 28.7-64 64-64H80z"/></svg>
                            @else
                                {{ \App\Helpers\FormatHelper::formatOdds($odd->{'spread_' . $team . '_point'}) }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            @if (is_null($odd->{'total_' . ($team === 'home' ? 'over' : 'under') . '_point'}))
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-auto" viewBox="0 0 448 512"><!-- Font Awesome Free 6.5.2 by @fontawesome --><path fill="#6b7280" d="M144 144v48H304V144c0-44.2-35.8-80-80-80s-80 35.8-80 80zM80 192V144C80 64.5 144.5 0 224 0s144 64.5 144 144v48h16c35.3 0 64 28.7 64 64V448c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V256c0-35.3 28.7-64 64-64H80z"/></svg>
                            @else
                                {{ \App\Helpers\FormatHelper::formatOdds($odd->{'total_' . ($team === 'home' ? 'over' : 'under') . '_point'}) }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            @if (is_null($odd->{'h2h_' . $team . '_price'}))
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-auto" viewBox="0 0 448 512"><!-- Font Awesome Free 6.5.2 by @fontawesome --><path fill="#6b7280" d="M144 144v48H304V144c0-44.2-35.8-80-80-80s-80 35.8-80 80zM80 192V144C80 64.5 144.5 0 224 0s144 64.5 144 144v48h16c35.3 0 64 28.7 64 64V448c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V256c0-35.3 28.7-64 64-64H80z"/></svg>
                            @else
                                {{ \App\Helpers\FormatHelper::formatOdds($odd->{'h2h_' . $team . '_price'}) }}
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="bg-gray-50 p-2 text-right">
            @if ($isCompleted)
                <div class="text-xs text-gray-700">
                    <p>Completed</p>
                </div>
            @else
                <div class="text-green-500 text-xs">
                    <p>{{ \Carbon\Carbon::parse($score->commence_time)->format('Y-m-d H:i:s') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
