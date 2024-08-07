<div class="bg-white p-6 rounded-lg shadow mb-6">
    <h2 class="text-xl font-semibold mb-4">Spread Records</h2>
    @if($records->isEmpty())
        <p>No spread records data available.</p>
    @else
        <table class="min-w-full bg-white rounded-lg shadow">
            <thead>
            <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                <th class="py-3 px-6 text-left">Description</th>
                <th class="py-3 px-6 text-left">Wins</th>
                <th class="py-3 px-6 text-left">Losses</th>
                <th class="py-3 px-6 text-left">Pushes</th>
            </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
            @foreach($records as $record)
                <tr class="border-b border-gray-200 hover:bg-gray-100">
                    <td class="py-3 px-6 text-left">{{ $record['type_description'] ?? 'N/A' }}</td>
                    <td class="py-3 px-6 text-left">{{ $record['wins'] ?? 'N/A' }}</td>
                    <td class="py-3 px-6 text-left">{{ $record['losses'] ?? 'N/A' }}</td>
                    <td class="py-3 px-6 text-left">{{ $record['pushes'] ?? 'N/A' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</div>
