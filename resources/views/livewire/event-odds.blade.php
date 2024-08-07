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
