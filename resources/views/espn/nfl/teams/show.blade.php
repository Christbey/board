<x-app-layout>
    <div class="container mx-auto py-8">
        <h1 class="text-2xl font-semibold mb-6">{{ $team->displayName }}</h1>
        @livewire('team-details', ['teamId' => $team->team_id])

        @livewire('team-injuries', ['teamId' => $team->team_id])

        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <h2 class="text-xl font-semibold mb-4">ATS Records</h2>
            @if($atsRecords->isEmpty())
                <p>No ATS records found for this team.</p>
            @else
                <table class="min-w-full bg-white rounded-lg shadow">
                    <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">Season</th>
                        <th class="py-3 px-6 text-left">Type</th>
                        <th class="py-3 px-6 text-left">Wins</th>
                        <th class="py-3 px-6 text-left">Losses</th>
                        <th class="py-3 px-6 text-left">Pushes</th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light">
                    @foreach($atsRecords as $record)
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6 text-left">{{ $record->season }}</td>
                            <td class="py-3 px-6 text-left">{{ $record->type_name }}</td>
                            <td class="py-3 px-6 text-left">{{ $record->wins }}</td>
                            <td class="py-3 px-6 text-left">{{ $record->losses }}</td>
                            <td class="py-3 px-6 text-left">{{ $record->pushes }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <h2 class="text-xl font-semibold mb-4">Events</h2>
            @if($events->isEmpty())
                <p>No events found for this team.</p>
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
                            <td class="py-3 px-6 text-left">{{ $event->home_team_score }}
                                - {{ $event->away_team_score }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <h2 class="text-xl font-semibold mb-4">Future Predictions</h2>
            @if($futures->isEmpty())
                <p>No future predictions available for this team.</p>
            @else
                <table class="min-w-full bg-white rounded-lg shadow">
                    <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">Future ID</th>
                        <th class="py-3 px-6 text-left">Name</th>
                        <th class="py-3 px-6 text-left">Display Name</th>
                        <th class="py-3 px-6 text-left">Provider ID</th>
                        <th class="py-3 px-6 text-left">Provider Name</th>
                        <th class="py-3 px-6 text-left">Athlete ID</th>
                        <th class="py-3 px-6 text-left">Team ID</th>
                        <th class="py-3 px-6 text-left">Value</th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light">
                    @foreach($futures as $future)
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6 text-left">{{ $future->future_id }}</td>
                            <td class="py-3 px-6 text-left">{{ $future->name }}</td>
                            <td class="py-3 px-6 text-left">{{ $future->display_name }}</td>
                            <td class="py-3 px-6 text-left">{{ $future->provider_id }}</td>
                            <td class="py-3 px-6 text-left">{{ $future->provider_name }}</td>
                            <td class="py-3 px-6 text-left">{{ $future->athlete_id }}</td>
                            <td class="py-3 px-6 text-left">{{ $future->team_id }}</td>
                            <td class="py-3 px-6 text-left">{{ $future->value }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <h2 class="text-xl font-semibold mb-4">Depth Chart</h2>
            @if($depthChart->isEmpty())
                <p>No depth chart data available for this team.</p>
            @else
                <table class="min-w-full bg-white rounded-lg shadow">
                    <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">Athlete</th>
                        <th class="py-3 px-6 text-left">Position</th>
                        <th class="py-3 px-6 text-left">Depth</th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light">
                    @foreach($depthChart as $item)
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6 text-left">{{ $item->athlete->full_name }}</td>
                            <td class="py-3 px-6 text-left">{{ $item->position }}</td>
                            <td class="py-3 px-6 text-left">{{ $item->depth }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <h2 class="text-xl font-semibold mb-4">Team Projections</h2>
        @if($projections->isEmpty())
            <p>No projections data available for this team.</p>
        @else
            <table class="min-w-full bg-white rounded-lg shadow">
                <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">Chance to Win Division</th>
                    <th class="py-3 px-6 text-left">Projected Wins</th>
                    <th class="py-3 px-6 text-left">Projected Losses</th>
                </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                @foreach($projections as $projection)
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left">{{ number_format($projection->chance_to_win_division, 2) }}%
                        </td>
                        <td class="py-3 px-6 text-left">{{ $projection->projected_wins }}</td>
                        <td class="py-3 px-6 text-left">{{ $projection->projected_losses }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
</x-app-layout>
