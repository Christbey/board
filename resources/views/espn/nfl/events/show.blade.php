<!-- resources/views/espn/nfl/events/show.blade.php -->

<x-app-layout>
    <div class="container mx-auto py-8">
        <h1 class="text-2xl font-semibold mb-6">{{ $event->name }}</h1>
        @livewire('event-details', ['eventId' => $event->id])
        @livewire('event-odds', ['eventId' => $event->event_id])
        @livewire('past-h2h-records', ['homeTeamId' => $event->home_team_id, 'awayTeamId' => $event->away_team_id])
    </div>
</x-app-layout>
