<!-- resources/views/ncaa/odds.blade.php -->

<x-app-layout>
    <div class="max-w-3xl py-6 mx-auto text-gray-900">
        <h1 class="text-2xl font-bold mb-6">Odds for {{ $sport }}</h1>
        <div class="grid grid-cols-1 gap-6">
            @foreach ($odds as $odd)
                <livewire:odds-card :odd="$odd" />
            @endforeach
        </div>
    </div>
</x-app-layout>
