<div class="team-info flex items-center mb-6">
    <img src="{{ $teamData['logos'][0]['href'] ?? '' }}" alt="Team Logo" class="w-24 h-auto mr-4">
    <div>
        <p><strong>Name:</strong> {{ $teamData['displayName'] ?? 'N/A' }}</p>
        <p><strong>Stadium:</strong> {{ $teamData['venue']['fullName'] ?? 'N/A' }}</p>
        <p><strong>Abbreviation:</strong> {{ $teamData['abbreviation'] ?? 'N/A' }}</p>
    </div>
</div>
