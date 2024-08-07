<x-app-layout>
    <div class="container mx-auto py-8">
        @livewire('team-details', ['teamId' => $team->team_id])
        @livewire('team-projections', ['teamId' => $team->team_id])
        @livewire('espn-team-stats', ['teamId' => $team->team_id])
        @livewire('team-spread-records', ['teamId' => $team->team_id])
        @livewire('team-injuries', ['teamId' => $team->team_id])
        @livewire('team-events', ['teamId' => $team->team_id])
        @livewire('team-future-predictions', ['teamId' => $team->team_id])
    </div>
</x-app-layout>