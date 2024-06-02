<!-- resources/views/livewire/odds-card.blade.php -->

<div class="relative bg-gray-100 text-gray-600 shadow-md rounded-lg p-6">
    <div class="flex justify-between mb-4">
        <p class="text-xs">{{ \Carbon\Carbon::parse($odd->commence_time)->setTimezone('America/Chicago')->format('D M jS') }}</p>
        @if(\Carbon\Carbon::parse($odd->commence_time)->lessThanOrEqualTo(now()) && \Carbon\Carbon::parse($odd->commence_time)->addHours(3)->greaterThan(now()))
            <div class="flex items-center">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                </span>
                <span class="ml-2 text-xs text-gray-600">Live</span>
            </div>
        @else
            <span class="text-xs">{{ \Carbon\Carbon::parse($odd->commence_time)->setTimezone('America/Chicago')->format('g:i A') }}</span>
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
                <p class="text-sm font-bold">{{ $odd->awayTeam->name }}</p>
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
                <p class="text-sm font-bold">{{ $odd->homeTeam->name }}</p>
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
