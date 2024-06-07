<!-- resources/views/odds/show.blade.php -->

<x-app-layout>
    <div class="max-w-3xl py-6 mx-auto text-gray-900">
        <h1 class="text-2xl font-bold mb-6">Odds for {{ $sport }}</h1>
        <livewire:date-picker :sport="$sport" />

            <div class="grid grid-cols-1 gap-6 mt-4">
                @foreach ($odds as $odd)
                    <livewire:odds-card :odd="$odd" :sport="$sport" />
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
