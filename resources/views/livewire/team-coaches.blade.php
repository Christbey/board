<ul class="list-none mb-6">
    @if($coaches)
        @foreach($coaches as $coach)
            <li class="mb-2 bg-white p-4 rounded shadow">
                <img src="{{ $coach['headshot']['href'] ?? '' }}" alt="{{ $coach['fullName'] ?? 'N/A' }}"
                     class="w-16 h-16 rounded-full mb-2">
                <p><strong>Name:</strong> {{ $coach['firstName'] ?? 'N/A' }} {{ $coach['lastName'] ?? 'N/A' }}</p>
                <p><strong>Role:</strong> {{ $coach['role'] ?? 'N/A' }}</p>
                <p><strong>Date of Birth:</strong> {{ $coach['dateOfBirth'] ?? 'N/A' }}</p>
                <p><strong>Experience:</strong> {{ $coach['experience'] ?? 'N/A' }} years</p>
            </li>
        @endforeach
    @else
        <li class="mb-2 bg-white p-4 rounded shadow">No coaches data available.</li>
    @endif
</ul>
