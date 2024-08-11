@php use Carbon\Carbon; @endphp
<x-app-layout>
    <div class="container mx-auto p-6">
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold">{{ $teamData->school }}</h2>
            <p>Mascot: {{ $teamData->mascot }}</p>
            <p>Abbreviation: {{ $teamData->abbreviation }}</p>
            <p>Conference: {{ $teamData->conference->name }}</p>
            <p>Location: {{ $teamData->city }}, {{ $teamData->state }}</p>
            <p>Stadium: {{ $teamData->venue_name }} (Capacity: {{ $teamData->capacity }})</p>
            <p>Elo Rating: {{ $eloRating->elo ?? 'N/A' }}</p>
            <p>FPI Rating: {{ $fpiRating->fpi ?? 'N/A' }}</p>
            <p>Strength of Record: {{ $fpiRating->strength_of_record ?? 'N/A' }}</p>
            <p>Average Win Probability: {{ $fpiRating->average_win_probability ?? 'N/A' }}</p>
            <p>Strength of Schedule: {{ $fpiRating->strength_of_schedule ?? 'N/A' }}</p>
            <p>Remaining Strength of Schedule: {{ $fpiRating->remaining_strength_of_schedule ?? 'N/A' }}</p>
            <p>Game Control: {{ $fpiRating->game_control ?? 'N/A' }}</p>
            <p>Overall Efficiency: {{ $fpiRating->efficiency_overall ?? 'N/A' }}</p>
            <p>Offensive Efficiency: {{ $fpiRating->efficiency_offense ?? 'N/A' }}</p>
            <p>Defensive Efficiency: {{ $fpiRating->efficiency_defense ?? 'N/A' }}</p>
            <p>Special Teams Efficiency: {{ $fpiRating->efficiency_special_teams ?? 'N/A' }}</p>
        </div>

        <h3 class="text-lg font-semibold mb-4">Games</h3>
        <div class="overflow-x-auto bg-white rounded-lg shadow-md">
            <table class="min-w-full bg-white">
                <thead>
                <tr class="bg-gray-100 border-b">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Opponent
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Location
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Result
                    </th>
                </tr>
                </thead>
                <tbody>
                @forelse($games as $game)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ Carbon::parse($game->start_date)->format('m/d') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($game->home_id == $teamData->id)
                                vs. {{ $game->awayTeam->school ?? 'Unknown Team' }}
                            @else
                                @ {{ $game->homeTeam->school ?? 'Unknown Team' }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $game->venue ?? 'Unknown Venue' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $game->result ?? 'TBD' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">No games available for this team.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
