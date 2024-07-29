<ul class="list-none mb-6">
    @if($records)
        @foreach($records as $record)
            <li class="mb-2 bg-white p-4 rounded shadow">
                <p><strong>Description:</strong> {{ $record['type']['description'] ?? 'N/A' }}</p>
                <p><strong>Wins:</strong> {{ $record['wins'] ?? 'N/A' }}</p>
                <p><strong>Losses:</strong> {{ $record['losses'] ?? 'N/A' }}</p>
                <p><strong>Pushes:</strong> {{ $record['pushes'] ?? 'N/A' }}</p>
            </li>
        @endforeach
    @else
        <li class="mb-2 bg-white p-4 rounded shadow">No spread records data available.</li>
    @endif
</ul>
