<ul class="list-none mb-6">
    @if($injuries)
        @foreach($injuries as $injury)
            <x-list-item>
                <p><strong>Status:</strong> {{ $injury['status'] ?? 'N/A' }}</p>
                <p><strong>Short Comment:</strong> {{ $injury['shortComment'] ?? 'N/A' }}</p>
                {{--
                                <p><strong>Long Comment:</strong> {{ $injury['longComment'] ?? 'N/A' }}</p>
                --}}
                <p><strong>Date:</strong> {{ $injury['date'] ?? 'N/A' }}</p>
                {{--
                                <p><strong>Athlete ID:</strong> {{ $injury['athlete']['id'] ?? 'N/A' }}</p>
                --}}
            </x-list-item>
        @endforeach
    @else
        <x-list-item>No injuries data available.</x-list-item>
    @endif
</ul>
