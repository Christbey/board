<!-- resources/views/odds/show.blade.php -->

<x-app-layout>
    <div class="max-w-3xl py-6 mx-auto text-gray-900">
        <h1 class="text-2xl font-bold mb-6">Odds for {{ $sport }}</h1>
        <div class="grid grid-cols-1 gap-6">
            @foreach ($odds as $odd)
                <div class="bg-gray-100 text-gray-600 shadow-md rounded-lg p-6">
                    <div class="flex flex-col">
                        <div class="col-span-2">
                            <p class="text-xs text-left">{{ \Carbon\Carbon::parse($odd['commence_time'])->setTimezone('America/Chicago')->format('D M jS') }}</p>
                        </div>
                        <div class="grid grid-cols-5 text-center mb-2">
                            <div class="col-span-2">
                            </div>
                            <div class="col-span-1">
                                <p class="text-xs font-semibold text-center uppercase">Spread</p>
                            </div>
                            <div class="col-span-1">
                                <p class="text-xs font-semibold text-center uppercase">Total</p>
                            </div>
                            <div class="col-span-1">
                                <p class="text-xs font-semibold uppercase">Moneyline</p>
                            </div>
                        </div>
                        @php
                            $spread_away = 'N/A';
                            $spread_home = 'N/A';
                            $total_over = 'N/A';
                            $total_under = 'N/A';
                            $moneyline_away = 'N/A';
                            $moneyline_home = 'N/A';

                            if (isset($odd['bookmakers'][0]['markets'])) {
                                foreach ($odd['bookmakers'][0]['markets'] as $market) {
                                    foreach ($market['outcomes'] as $outcome) {
                                        if ($market['key'] == 'spreads') {
                                            if ($outcome['name'] == $odd['away_team']) {
                                                $spread_away = $outcome['point'] ?? 'N/A';
                                            } elseif ($outcome['name'] == $odd['home_team']) {
                                                $spread_home = $outcome['point'] ?? 'N/A';
                                            }
                                        } elseif ($market['key'] == 'totals') {
                                            if ($outcome['name'] == 'Over') {
                                                $total_over = $outcome['point'] ?? 'N/A';
                                            } elseif ($outcome['name'] == 'Under') {
                                                $total_under = $outcome['point'] ?? 'N/A';
                                            }
                                        } elseif ($market['key'] == 'h2h') {
                                            if ($outcome['name'] == $odd['away_team']) {
                                                $moneyline_away = $outcome['price'] ?? 'N/A';
                                            } elseif ($outcome['name'] == $odd['home_team']) {
                                                $moneyline_home = $outcome['price'] ?? 'N/A';
                                            }
                                        }
                                    }
                                }
                            }
                        @endphp
                        <div class="grid grid-cols-5 text-center mb-2">
                            <div class="col-span-2 text-left">
                                <p class="text-sm font-bold">{{ $odd['away_team'] }}</p>
                            </div>
                            <div class="col-span-1 text-center">
                                <p class="text-sm">{{ $spread_away }}</p>
                            </div>
                            <div class="col-span-1 text-center">
                                <p class="text-sm">{{ $total_over }}</p>
                            </div>
                            <div class="col-span-1 text-center">
                                <p class="text-sm">{{ $moneyline_away }}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-5 text-center">
                            <div class="col-span-2 text-left">
                                <p class="text-sm font-bold">{{ $odd['home_team'] }}</p>
                            </div>
                            <div class="col-span-1 text-center">
                                <p class="text-sm">{{ $spread_home }}</p>
                            </div>
                            <div class="col-span-1 text-center">
                                <p class="text-sm">{{ $total_under }}</p>
                            </div>
                            <div class="col-span-1 text-center">
                                <p class="text-sm">{{ $moneyline_home }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <!-- Debugging Section -->
    <pre>{{ print_r($odds) }}</pre>
</x-app-layout>
