<div class="bg-white p-8 rounded-lg shadow mb-8">
    <h2 class="text-2xl p-6 font-semibold">Past Head-to-Head Records</h2>
    @if($records->isEmpty())
        <p class="text-gray-600">No past head-to-head records available for these teams.</p>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-lg shadow-md">
                <thead>
                <tr class="bg-gray-800 text-white uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">Event ID</th>
                    <th class="py-3 px-6 text-left">Home Team ID</th>
                    <th class="py-3 px-6 text-left">Away Team ID</th>
                    <th class="py-3 px-6 text-left">Spread</th>
                    <th class="py-3 px-6 text-left">Over Odds</th>
                    <th class="py-3 px-6 text-left">Under Odds</th>
                    <th class="py-3 px-6 text-left">Total Line</th>
                    <th class="py-3 px-6 text-left">Total Result</th>
                </tr>
                </thead>
                <tbody class="text-gray-700 text-sm font-light">
                @foreach($records as $record)
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left">{{ $record->event_id }}</td>
                        <td class="py-3 px-6 text-left">{{ $record->home_team_id }}</td>
                        <td class="py-3 px-6 text-left">{{ $record->away_team_id }}</td>
                        <td class="py-3 px-6 text-left">{{ $record->spread }}</td>
                        <td class="py-3 px-6 text-left">{{ $record->over_odds }}</td>
                        <td class="py-3 px-6 text-left">{{ $record->under_odds }}</td>
                        <td class="py-3 px-6 text-left">{{ $record->total_line }}</td>
                        <td class="py-3 px-6 text-left">{{ $record->total_result }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
