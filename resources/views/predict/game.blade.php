@php use Carbon\Carbon; @endphp
<x-app-layout>

    <div class="flex items-center justify-center">
        <div class="p-8 rounded-3xl bg-white max-w-lg w-full shadow-xl">
            @if(isset($error))
                <div class="flex items-center space-x-2 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg"
                     role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 shrink-0" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <div class="flex-1">
                        <h3 class="font-medium text-lg text-gray-900">Error</h3>
                        <p class="text-sm font-normal text-gray-400 mt-1">{{ $error }}</p>
                    </div>
                </div>
            @else
                <div aria-label="header" class="flex flex-col items-left space-y-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-indigo-600" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 3l0 7l6 0l-8 11l0 -7l-6 0l8 -11"/>
                    </svg>
                    <div class="text-center">
                        <h3 class="font-medium text-lg tracking-tight text-gray-900 text-left">Prediction
                            for {{ $prediction['away_team'] }} vs {{ $prediction['home_team'] }}</h3>
                        <p class="text-sm text-left font-normal text-gray-400">AI-Powered Sports Analytics</p>
                    </div>
                </div>

                <div aria-label="content" class="mt-4 grid gap-2.5">
                    <div class="flex items-center space-x-4 p-4 rounded-2xl bg-gray-100">
                        <div class="flex-1">
                            <h3 class="text-sm font-medium text-gray-800">Prediction Details</h3>
                            <div class="text-gray-700">
                                <p class="text-sm">Hypothetical Spread: <span
                                            class="font-bold text-blue-600">{{ $prediction['hypothetical_spread'] }}</span>
                                </p>
                                <p class="text-sm">{{ $prediction['home_team'] }} Win Probability: <span
                                            class="text-blue-600">{{ $prediction['home_win_prob'] }}%</span></p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4 p-4 rounded-2xl bg-gray-100">
                        <div class="flex-1">
                            <h3 class="text-sm font-medium text-gray-800">Game Data</h3>
                            <div class="text-gray-700">
                                @if(isset($prediction['odds']['total']['over']['points']) && isset($prediction['odds']['total']['over']['price']))
                                    <p class="text-sm">
                                        <strong>Over:</strong> {{ $prediction['odds']['total']['over']['points'] }}
                                        ({{ $prediction['odds']['total']['over']['price'] }})
                                    </p>
                                @endif

                                @if(isset($prediction['odds']['home_spread']['points']) && isset($prediction['odds']['home_spread']['price']))
                                    <p class="text-sm">
                                        <strong>Home Spread:</strong> {{ $prediction['odds']['home_spread']['points'] }}
                                        ({{ $prediction['odds']['home_spread']['price'] }})
                                    </p>
                                @endif

                                @if(isset($prediction['actual_home_score']))
                                    <p class="text-sm">
                                        <strong>Actual Home Score:</strong> {{ $prediction['actual_home_score'] }}
                                    </p>
                                @endif

                                @if(isset($prediction['actual_away_score']))
                                    <p class="text-sm">
                                        <strong>Actual Away Score:</strong> {{ $prediction['actual_away_score'] }}
                                    </p>
                                @endif

                                <p class="text-sm">
                                    <strong>Game Completed:</strong>
                                    <span class="{{ $prediction['game_completed'] ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $prediction['game_completed'] ? 'Yes' : 'No' }}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4 p-4 mb-3 rounded-2xl bg-gray-100">
                        <div class="flex-1">
                            <h3 class="text-sm font-medium text-gray-800">Odds Details</h3>
                            <div class="text-gray-700">
                                @if(isset($prediction['odds']['home_moneyline']) || isset($prediction['odds']['away_moneyline']))
                                    <p class="text-sm">
                                        <strong>Moneyline:</strong>
                                        Home {{ $prediction['odds']['home_moneyline'] ?? 'Not Available' }} /
                                        Away {{ $prediction['odds']['away_moneyline'] ?? 'Not Available' }}
                                    </p>
                                @endif

                                @if(isset($prediction['odds']['total']['over']['points']) && isset($prediction['odds']['total']['over']['price']))
                                    <p class="text-sm">
                                        <strong>Total Points:</strong>
                                        {{ $prediction['odds']['total']['over']['points'] }}
                                        ({{ $prediction['odds']['total']['over']['price'] }})
                                    </p>
                                @endif

                                @if(isset($prediction['odds']['bookmaker']))
                                    <p class="text-sm">
                                        <strong>Bookmaker:</strong> {{ ucfirst($prediction['odds']['bookmaker']) }}
                                    </p>
                                @endif

                                @if(isset($prediction['odds']['commence_time']))
                                    <p class="text-sm">
                                        <strong>Commence Time:</strong>
                                        {{ Carbon::parse($prediction['odds']['commence_time'])->format('Y-m-d H:i') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            @endif
            <div class="mt-6 text-center">
                <a href="{{ route('predict.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 active:bg-blue-600 disabled:opacity-25 transition">
                    Go Back
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
