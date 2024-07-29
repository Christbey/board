<div class="team-info flex items-center mb-6">
    <img src="{{ $teamData['logos'][0]['href'] ?? '' }}" alt="Team Logo" class="w-24 h-auto mr-4">
    <div>
        <p><strong>Name:</strong> {{ $teamData['displayName'] ?? 'N/A' }}</p>
        <p><strong>Location:</strong> {{ $teamData['location'] ?? 'N/A' }}</p>
        <p><strong>Abbreviation:</strong> {{ $teamData['abbreviation'] ?? 'N/A' }}</p>
        <p><strong>Color:</strong> <span
                    style="color:#{{ $teamData['color'] ?? '000000' }}">{{ $teamData['color'] ?? 'N/A' }}</span></p>
        <p><strong>Alternate Color:</strong> <span
                    style="color:#{{ $teamData['alternateColor'] ?? '000000' }}">{{ $teamData['alternateColor'] ?? 'N/A' }}</span>
        </p>
        <p><strong>Venue:</strong> {{ $teamData['venue']['fullName'] ?? 'N/A' }}</p>
    </div>
</div>
