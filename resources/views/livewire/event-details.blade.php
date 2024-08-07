<div class="bg-white p-6 rounded-lg shadow mb-6">
    <h2 class="text-xl font-semibold mb-4">Event Details</h2>
    @if($event)
        <p><strong>Date:</strong> {{ $event->status_type_detail }}</p>
        <p><strong>Venue:</strong> {{ $event->venue_name }}, {{ $event->venue_city }}, {{ $event->venue_state }}</p>
        <p><strong>Score:</strong> {{ $event->home_team_score }} - {{ $event->away_team_score }}</p>
    @else
        <p>No event details available.</p>
    @endif
</div>
