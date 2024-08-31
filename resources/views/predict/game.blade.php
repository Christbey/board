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
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-indigo-600" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 3l0 7l6 0l-8 11l0 -7l-6 0l8 -11"/>
                        </svg>
                        <span class="ml-2 text-lg font-bold text-blue-600">{{ $prediction['hypothetical_spread'] }}</span>
                    </div>
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
                                <p class="text-sm">
                                    {{ $prediction['home_team'] }} has a <span class="font-bold text-blue-600">{{ $prediction['home_win_prob'] }}%</span>
                                    chance to win, The Over/Under is <span
                                            class="font-bold text-blue-600">{{ $prediction['odds']['total']['over']['points'] }}</span>.
                                    @if(isset($prediction['odds']['home_spread']['points']) && isset($prediction['odds']['home_spread']['price']))
                                        The spread is <span
                                                class="font-bold text-blue-600">{{ $prediction['odds']['home_spread']['points'] }}</span>
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
                        </div>
                    </div>

                    <!-- Additional content as needed -->

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
