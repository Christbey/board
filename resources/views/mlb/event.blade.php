<x-app-layout>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">MLB Game Details</h1>
        <livewire:date-picker :selectedDate="$selectedDate" :sport="'mlb'" />

        @if($scores->isEmpty())
            <p class="text-center text-gray-500">No results found for the selected date.</p>
        @else
            @foreach ($scores as $score)
                @php
                    $odd = $odds->where('event_id', $score->event_id)->first();
                @endphp

                @if ($odd)
                    <livewire:event-card :score="$score" :odd="$odd" :winnerColor="$score->winner_color" />
                @endif
            @endforeach
        @endif
    </div>
</x-app-layout>
