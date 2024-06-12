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
                @php
                    $homeScore = $score->home_team_score ?? 0;
                    $awayScore = $score->away_team_score ?? 0;
                    $homeIsWinner = $homeScore > $awayScore;
                    $awayIsWinner = $awayScore > $homeScore;
                    $homeColor = $homeIsWinner ? '#' . ltrim($score->homeTeam->primary_color, '#') : '';
                    $awayColor = $awayIsWinner ? '#' . ltrim($score->awayTeam->primary_color, '#') : '';
                    $homeSpreadCovered = $homeScore + $odd->spread_home_point > $awayScore;
                    $awaySpreadCovered = $awayScore + $odd->spread_away_point > $homeScore;

                    $totalScore = $homeScore + $awayScore;
                    $totalOverPoint = $odd->total_over_point ?? 0;
                    $totalUnderPoint = $odd->total_under_point ?? 0;
                    $isOver = $isUnder = false;

                    if ($isCompleted) {
                        $isOver = $totalScore > $totalOverPoint;
                        $isUnder = $totalScore < $totalUnderPoint;
                    }
                @endphp
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm {{ $homeIsWinner ? 'font-bold' : 'font-medium text-gray-900' }}" style="color: {{ $homeColor }}">{{ $score->homeTeam->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center {{ $homeIsWinner ? 'font-bold' : 'text-gray-500' }}" style="color: {{ $homeColor }}">{{ $homeScore }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center {{ $homeSpreadCovered ? 'font-bold' : 'text-gray-500' }}" style="color: {{ $homeSpreadCovered ? $homeColor : '' }}">
                        @if (is_null($odd->spread_home_point))
                            @include('components.lock-icon')
                        @else
                            {{ \App\Helpers\FormatHelper::formatOdds($odd->spread_home_point) }}
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                        @if (is_null($odd->total_over_point))
                            @include('components.lock-icon')
                        @else
                            <span class="{{ $isCompleted && $isOver ? 'font-bold' : 'text-gray-500' }}" style="color: {{ $isCompleted && $isOver ? $homeColor : '' }}">
                                {{ \App\Helpers\FormatHelper::formatOdds($odd->total_over_point, 'total_home') }}
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center {{ $homeIsWinner ? 'font-bold' : 'text-gray-500' }}" style="color: {{ $homeColor }}">
                        @if (is_null($odd->h2h_home_price))
                            @include('components.lock-icon')
                        @else
                            {{ \App\Helpers\FormatHelper::formatOdds($odd->h2h_home_price) }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm {{ $awayIsWinner ? 'font-bold' : 'font-medium text-gray-900' }}" style="color: {{ $awayColor }}">{{ $score->awayTeam->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center {{ $awayIsWinner ? 'font-bold' : 'text-gray-500' }}" style="color: {{ $awayColor }}">{{ $awayScore }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center {{ $awaySpreadCovered ? 'font-bold' : 'text-gray-500' }}" style="color: {{ $awaySpreadCovered ? $awayColor : '' }}">
                        @if (is_null($odd->spread_away_point))
                            @include('components.lock-icon')
                        @else
                            {{ \App\Helpers\FormatHelper::formatOdds($odd->spread_away_point) }}
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                        @if (is_null($odd->total_under_point))
                            @include('components.lock-icon')
                        @else
                            <span class="{{ $isCompleted && $isUnder ? 'font-bold' : 'text-gray-500' }}" style="color: {{ $isCompleted && $isUnder ? $awayColor : '' }}">
                                {{ \App\Helpers\FormatHelper::formatOdds($odd->total_under_point, 'total_away') }}
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center {{ $awayIsWinner ? 'font-bold' : 'text-gray-500' }}" style="color: {{ $awayColor }}">
                        @if (is_null($odd->h2h_away_price))
                            @include('components.lock-icon')
                        @else
                            {{ \App\Helpers\FormatHelper::formatOdds($odd->h2h_away_price) }}
                        @endif
                    </td>
                </tr>
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
