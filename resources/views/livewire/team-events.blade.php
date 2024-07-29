<ul class="list-none mb-6">
    @if($events)
        @foreach($events as $event)
            <li class="mb-2 bg-white p-4 rounded shadow">
                <p><strong>Event Name:</strong> {{ $event['name'] ?? 'N/A' }}</p>
                <p><strong>Short Name:</strong> {{ $event['shortName'] ?? 'N/A' }}</p>
                <p><strong>Date:</strong> {{ $event['date'] ?? 'N/A' }}</p>

                @if(isset($event['predictor']))
                    <h4 class="text-xl font-semibold mt-2">Predictor:</h4>

                    <h5 class="text-lg font-semibold mt-2">Home Team:</h5>
                    <table class="table-auto w-full mb-4">
                        <thead>
                        <tr>
                            <th class="px-4 py-2">Statistic</th>
                            <th class="px-4 py-2">Value</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($event['predictor']['homeTeam']['statistics'] as $stat)
                            <tr>
                                <td class="border px-4 py-2">{{ $stat['displayName'] }}</td>
                                <td class="border px-4 py-2">{{ $stat['displayValue'] }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <h5 class="text-lg font-semibold mt-2">Away Team:</h5>
                    <table class="table-auto w-full mb-4">
                        <thead>
                        <tr>
                            <th class="px-4 py-2">Statistic</th>
                            <th class="px-4 py-2">Value</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($event['predictor']['awayTeam']['statistics'] as $stat)
                            <tr>
                                <td class="border px-4 py-2">{{ $stat['displayName'] }}</td>
                                <td class="border px-4 py-2">{{ $stat['displayValue'] }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            </li>
        @endforeach
    @else
        <li class="mb-2 bg-white p-4 rounded shadow">No events data available.</li>
    @endif
</ul>
