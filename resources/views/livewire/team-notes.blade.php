<ul class="list-none mb-6">
    @if($notes)
        @foreach($notes as $note)
            <x-list-item>
                <p><strong>Date:</strong> {{ $note['date'] ?? 'N/A' }}</p>
                <p><strong>Comment:</strong> {{ $note['comment'] ?? 'N/A' }}</p>
            </x-list-item>
        @endforeach
    @else
        <x-list-item>No notes data available.</x-list-item>
    @endif
</ul>
