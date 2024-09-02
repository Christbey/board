@php use Carbon\Carbon; @endphp
<x-app-layout>
    <div class="flex items-center pt-24 justify-center px-4 sm:px-6 lg:px-8">
        <div class="p-8 rounded-3xl bg-white max-w-lg w-full shadow-xl">
            @if(isset($error))
                <div class="flex items-center space-x-2 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg"
                     role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 shrink-0" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <div class="flex-1">
                        <h3 class="font-medium text-lg">Error</h3>
                        <p class="text-sm mt-1">{{ $error }}</p>
                    </div>
                </div>
            @else
                <div aria-label="header" class="flex flex-col items-center space-y-2 mb-6">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-indigo-600" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 3l0 7l6 0l-8 11l0 -7l-6 0l8 -11"/>
                        </svg>
                        <span class="ml-2 text-lg font-bold text-indigo-600">{{ $prediction['hypothetical_spread'] }}</span>
                    </div>
                    <div class="text-center">
                        <h3 class="font-medium text-lg tracking-tight text-gray-900">Prediction
                            for {{ $prediction['away_team'] }} vs {{ $prediction['home_team'] }}</h3>
                        <p class="text-sm text-gray-500">AI-Powered Sports Analytics</p>
                    </div>
                </div>

                <div aria-label="content" class="space-y-4">
                    @if(!$prediction['game_completed'])
                        <div class="p-4 rounded-2xl bg-gray-50">
                            <h3 class="text-sm font-medium text-gray-800 mb-2">Analysis</h3>
                            <p class="text-gray-700 text-sm">
                                {{ $prediction['home_team'] }} has a <span class="font-bold text-indigo-600">{{ $prediction['home_win_prob'] }}%</span>
                                chance to win.
                                The Over/Under is <span
                                        class="font-bold text-indigo-600">{{ $prediction['odds']['total']['under']['points'] ?? 'NA' }}</span>.
                                @if(isset($prediction['odds']['home_spread']['points']) && isset($prediction['odds']['home_spread']['price']))
                                    The spread is <span
                                            class="font-bold text-indigo-600">{{ $prediction['odds']['home_spread']['points'] }}</span>
                                    ({{ $prediction['odds']['home_spread']['price'] }}).
                                @endif
                                Kickoff is scheduled for
                                <span class="font-bold">
                                @if(Carbon::parse($prediction['odds']['commence_time'])->isToday())
                                        {{ Carbon::parse($prediction['odds']['commence_time'])->format('g:i A') }}
                                    @else
                                        {{ Carbon::parse($prediction['odds']['commence_time'])->format('Y-m-d H:i') }}
                                    @endif
                </span>.
                            </p>
                        </div>

                        <div class="flex space-x-4">
                            @if(isset($prediction['actual_away_score']))
                                <div class="flex w-full items-center justify-between bg-gray-100 p-3 rounded-lg">
                                    <p class="text-sm font-medium" style="color: {{$prediction['away_team_color']}};">
                                        {{$prediction['away_team']}} Score:
                                    </p>
                                    <p class="text-sm" style="color: {{$prediction['away_team_color']}};">
                                        {{ $prediction['actual_away_score'] }}
                                    </p>
                                </div>
                            @endif

                            @if(isset($prediction['actual_home_score']))
                                <div class="flex items-center w-full justify-between bg-gray-100 p-3 rounded-lg">
                                    <p class="text-sm font-medium" style="color: {{$prediction['home_team_color']}};">
                                        {{$prediction['home_team']}} Score:
                                    </p>
                                    <p class="text-sm" style="color: {{$prediction['home_team_color']}};">
                                        {{ $prediction['actual_home_score'] }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if($prediction['game_completed'])
                        <div class="flex items-center justify-between bg-gray-100 p-3 rounded-lg">
                            <p class="text-sm font-medium text-gray-800">Game Completed:</p>
                            <p class="text-sm text-green-600">
                                Yes
                            </p>
                        </div>
                    @endif
                </div>
            @endif

            <div class="mt-6 text-center">
                <a href="{{ route('predict.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                    Go Back
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
