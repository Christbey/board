<x-app-layout>
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">MLB Game Details</h1>

    @foreach ($scores as $score)
        @php
            $odd = $odds->where('event_id', $score->event_id)->first();
        @endphp

        @if ($odd)
            <livewire:mlb-score-odds-card :score="$score" :odd="$odd" />
        @endif
    @endforeach
</div>
</x-app-layout>