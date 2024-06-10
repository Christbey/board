<x-app-layout>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">NBA Game Details</h1>
        <livewire:date-picker :selectedDate="$selectedDate" :sport="'nba'" />

        @if ($scores->isEmpty())
            <div class="text-center text-gray-500">
                <p>No results found for the selected date.</p>
            </div>
        @else
            @foreach ($scores as $score)
                @php
                    $odd = $odds->where('event_id', $score->event_id)->first();
                @endphp

                @if ($odd)
                    <livewire:event-card :score="$score" :odd="$odd" />
                @endif
            @endforeach
        @endif
    </div>
</x-app-layout>
