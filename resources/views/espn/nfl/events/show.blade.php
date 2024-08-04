@php use Carbon\Carbon; @endphp
        <!-- resources/views/espn/nfl/events/show.blade.php -->

<x-app-layout>
    <div class="container mx-auto py-8">
        <h1 class="text-2xl font-semibold mb-6">{{ $event->name }}</h1>
        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <h2 class="text-xl font-semibold mb-4">Event Details</h2>
            <p><strong>Event ID:</strong> {{ $event->event_id }}</p>
            <p><strong>Date:</strong> {{ $event->date }}</p>
            <p><strong>Venue:</strong> {{ $event->venue_name }}, {{ $event->venue_city }}, {{ $event->venue_state }}</p>
            <p><strong>Home Team:</strong> {{ $event->home_team_id }}</p>
            <p><strong>Away Team:</strong> {{ $event->away_team_id }}</p>
            <p><strong>Score:</strong> {{ $event->home_team_score }} - {{ $event->away_team_score }}</p>
            <p><strong>Attendance:</strong> {{ $event->attendance }}</p>
            <p><strong>Status:</strong> {{ $event->status_type_name }}</p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <h2 class="text-xl font-semibold mb-4">Event Odds</h2>
            @if($odds->isEmpty())
                <p>No odds data available for this event.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white rounded-lg shadow">
                        <thead>
                        <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">Provider Name</th>
                            <th class="py-3 px-6 text-left">Over/Under</th>
                            <th class="py-3 px-6 text-left">Spread</th>
                            <th class="py-3 px-6 text-left">Over Odds</th>
                            <th class="py-3 px-6 text-left">Under Odds</th>
                        </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-light">
                        @foreach($odds as $odd)
                            <tr class="border-b border-gray-200 hover:bg-gray-100">
                                <td class="py-3 px-6 text-left">{{ $odd->provider_name }}</td>
                                <td class="py-3 px-6 text-left">{{ $odd->over_under }}</td>
                                <td class="py-3 px-6 text-left">{{ $odd->spread }}</td>
                                <td class="py-3 px-6 text-left">{{ $odd->over_odds }}</td>
                                <td class="py-3 px-6 text-left">{{ $odd->under_odds }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        @if($pastH2h->isNotEmpty())
            <div class="bg-white p-6 rounded-lg shadow mb-6">
                <h2 class="text-xl font-semibold mb-4">Past Head-to-Head</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white rounded-lg shadow">
                        <thead>
                        <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">Event ID</th>
                            <th class="py-3 px-6 text-left">Home Team ID</th>
                            <th class="py-3 px-6 text-left">Away Team ID</th>
                            <th class="py-3 px-6 text-left">Spread</th>
                            <th class="py-3 px-6 text-left">Over Odds</th>
                            <th class="py-3 px-6 text-left">Under Odds</th>
                            <th class="py-3 px-6 text-left">Money Line Odds (Away)</th>
                            <th class="py-3 px-6 text-left">Money Line Odds (Home)</th>
                            <th class="py-3 px-6 text-left">Spread Winner (Away)</th>
                            <th class="py-3 px-6 text-left">Spread Winner (Home)</th>
                            <th class="py-3 px-6 text-left">Money Line Winner (Away)</th>
                            <th class="py-3 px-6 text-left">Money Line Winner (Home)</th>
                            <th class="py-3 px-6 text-left">Line Date</th>
                            <th class="py-3 px-6 text-left">Total Line</th>
                            <th class="py-3 px-6 text-left">Total Result</th>
                        </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-light">
                        @foreach($pastH2h as $h2h)
                            <tr class="border-b border-gray-200 hover:bg-gray-100">
                                <td class="py-3 px-6 text-left">{{ $h2h->event_id }}</td>
                                <td class="py-3 px-6 text-left">{{ $h2h->home_team_id }}</td>
                                <td class="py-3 px-6 text-left">{{ $h2h->away_team_id }}</td>
                                <td class="py-3 px-6 text-left">{{ number_format($h2h->spread, 2) }}</td>
                                <td class="py-3 px-6 text-left">{{ number_format($h2h->over_odds, 2) }}</td>
                                <td class="py-3 px-6 text-left">{{ number_format($h2h->under_odds, 2) }}</td>
                                <td class="py-3 px-6 text-left">{{ number_format($h2h->away_team_money_line_odds, 2) }}</td>
                                <td class="py-3 px-6 text-left">{{ number_format($h2h->home_team_money_line_odds, 2) }}</td>
                                <td class="py-3 px-6 text-left">{{ $h2h->away_team_spread_winner ? 'Yes' : 'No' }}</td>
                                <td class="py-3 px-6 text-left">{{ $h2h->home_team_spread_winner ? 'Yes' : 'No' }}</td>
                                <td class="py-3 px-6 text-left">{{ $h2h->away_team_money_line_winner ? 'Yes' : 'No' }}</td>
                                <td class="py-3 px-6 text-left">{{ $h2h->home_team_money_line_winner ? 'Yes' : 'No' }}</td>
                                <td class="py-3 px-6 text-left">{{ Carbon::parse($h2h->line_date)->format('Y-m-d H:i:s') }}</td>
                                <td class="py-3 px-6 text-left">{{ number_format($h2h->total_line, 2) }}</td>
                                <td class="py-3 px-6 text-left">{{ $h2h->total_result }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
