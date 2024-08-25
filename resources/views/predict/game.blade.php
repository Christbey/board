<x-app-layout>
    <div class="container mx-auto py-8 px-4">
        @if(isset($error))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-8" role="alert">
                <strong class="font-bold">Error:</strong>
                <span class="block sm:inline">{{ $error }}</span>
            </div>
        @else
            <div class="bg-white shadow-lg rounded-lg p-6 mb-8">
                <h1 class="text-3xl font-extrabold mb-6 text-gray-900">Prediction for {{ $prediction['away_team'] }}
                    vs {{ $prediction['home_team'] }}</h1>

                <div class="mb-8">
                    <p class="text-xl font-semibold text-gray-800">
                        @if($prediction['game_completed'])
                            Winner: <span class="text-green-600">{{ $prediction['winner'] }}</span>
                        @else
                            Predicted Winner: <span class="text-green-600">{{ $prediction['winner'] }}</span>
                        @endif
                    </p>
                </div>

                <div class="bg-gray-50 p-6 rounded-lg shadow-inner">
                    <h3 class="text-2xl font-semibold mb-4 text-gray-800">Game Data</h3>
                    <p class="mb-2"><strong>Opening Spread:</strong> <span
                                class="text-gray-700">{{ $prediction['spread'] }}</span></p>
                    <p class="mb-2"><strong>Live Spread:</strong> <span
                                class="text-gray-700">{{ $prediction['odds']->spread_home_point }}</span></p>
                    <p class="mb-2"><strong>{{ $prediction['home_team'] }}:</strong> <span
                                class="text-gray-700">{{ $prediction['actual_home_score'] }}</span></p>
                    <p class="mb-2"><strong>{{ $prediction['away_team'] }}:</strong> <span
                                class="text-gray-700">{{ $prediction['actual_away_score'] }}</span></p>
                    <p><strong>{{ $prediction['home_team'] }} ML:</strong> <span
                                class="text-gray-700">{{ $prediction['home_win_prob'] }}%</span></p>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
