{{-- resources/views/nfl/odds.blade.php --}}
<x-app-layout>
    <div class="max-w-3xl py-6 mx-auto text-gray-900">
        <h1 class="text-2xl font-bold mb-6">Odds for {{ $sport }}</h1>

        <!-- Date Picker Form -->
        <form method="GET" action="{{ route('nfl.odds') }}">
            <div class="mb-4">
                <input type="date" name="date" value="{{ request('date', now()->format('Y-m-d')) }}" class="border rounded px-3 py-2">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Filter</button>
            </div>
        </form>

        <div class="grid grid-cols-1 gap-6">
            @foreach ($odds as $odd)
                <livewire:odds-card :odd="$odd" :sport="$sport" />
            @endforeach
        </div>
    </div>
</x-app-layout>
