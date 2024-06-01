<div class="relative bg-gray-100 text-gray-600 shadow-md rounded-lg p-6">
    <div class="flex justify-between mb-4">
        <p class="text-xs">{{ \Carbon\Carbon::parse($odd['commence_time'])->setTimezone('America/Chicago')->format('D M jS') }}</p>
        @if(\Carbon\Carbon::parse($odd['commence_time'])->lessThanOrEqualTo(now()) && \Carbon\Carbon::parse($odd['commence_time'])->addHours(3)->greaterThan(now()))
            <div class="flex items-center">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                </span>
                <span class="ml-2 text-xs text-gray-600">Live</span>
            </div>
        @else
            <span class="text-xs">{{ \Carbon\Carbon::parse($odd['commence_time'])->setTimezone('America/Chicago')->format('g:i A') }}</span>
        @endif
    </div>
    <div class="flex flex-col">
        <div class="grid grid-cols-5 text-center mb-2">
            <div class="col-span-2"></div>
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
        <div class="grid grid-cols-5 text-center mb-2">
            <div class="col-span-2 text-left">
                <p class="text-sm font-bold">{{ $odd['away_team'] }}</p>
            </div>
            <div class="col-span-1 text-center">
                <p class="text-sm">
                    @if(is_null($spread_away) || $spread_away == 'N/A')
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mx-auto">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                    @else
                        {{ $spread_away }}
                    @endif
                </p>
            </div>
            <div class="col-span-1 text-center">
                <p class="text-sm">
                    @if(is_null($total_over) || $total_over == 'N/A')
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mx-auto">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                    @else
                        {{ $total_over }}
                    @endif
                </p>
            </div>
            <div class="col-span-1 text-center">
                <p class="text-sm">
                    @if(is_null($moneyline_away) || $moneyline_away == 'N/A')
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mx-auto">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                    @else
                        {{ $moneyline_away }}
                    @endif
                </p>
            </div>
        </div>
        <div class="grid grid-cols-5 text-center">
            <div class="col-span-2 text-left">
                <p class="text-sm font-bold">{{ $odd['home_team'] }}</p>
            </div>
            <div class="col-span-1 text-center">
                <p class="text-sm">
                    @if(is_null($spread_home) || $spread_home == 'N/A')
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mx-auto">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                    @else
                        {{ $spread_home }}
                    @endif
                </p>
            </div>
            <div class="col-span-1 text-center">
                <p class="text-sm">
                    @if(is_null($total_under) || $total_under == 'N/A')
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mx-auto">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                    @else
                        {{ $total_under }}
                    @endif
                </p>
            </div>
            <div class="col-span-1 text-center">
                <p class="text-sm">
                    @if(is_null($moneyline_home) || $moneyline_home == 'N/A')
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mx-auto">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                    @else
                        {{ $moneyline_home }}
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>
