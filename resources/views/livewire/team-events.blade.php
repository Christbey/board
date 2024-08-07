<div class="bg-white p-6 rounded-lg shadow mb-6">
    <h2 class="text-xl font-semibold mb-4">Events</h2>
    @if($events->isEmpty())
        <p>No events found for this team for the current year.</p>
    @else
        <table class="min-w-full bg-white rounded-lg shadow">
            <thead>
            <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                <th class="py-3 px-6 text-left">Event Name</th>
                <th class="py-3 px-6 text-left">Date</th>
                <th class="py-3 px-6 text-left">Venue</th>
                <th class="py-3 px-6 text-left">Home Team</th>
                <th class="py-3 px-6 text-left">Away Team</th>
                <th class="py-3 px-6 text-left">Score</th>
            </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
            @foreach($events as $event)
                <tr class="border-b border-gray-200 hover:bg-gray-100">
                    <td class="py-3 px-6 text-left">
                        <a href="{{ route('espn.events.show', $event->id) }}"
                           class="text-blue-500 hover:underline">{{ $event->name }}</a>
                    </td>
                    <td class="py-3 px-6 text-left">{{ $event->date }}</td>
                    <td class="py-3 px-6 text-left">{{ $event->venue_name }}</td>
                    <td class="py-3 px-6 text-left">{{ $event->home_team_id }}</td>
                    <td class="py-3 px-6 text-left">{{ $event->away_team_id }}</td>
                    <td class="py-3 px-6 text-left">{{ $event->home_team_score }} - {{ $event->away_team_score }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</div>
