<div class="bg-white p-6 rounded-lg shadow mb-6">
    <h2 class="text-xl font-semibold mb-4">Team Details</h2>
    <p><strong>ID:</strong> {{ $team->id }}</p>
    <p><strong>UID:</strong> {{ $team->uid }}</p>
    <p><strong>Slug:</strong> {{ $team->slug }}</p>
    <p><strong>Abbreviation:</strong> {{ $team->abbreviation }}</p>
    <p><strong>Display Name:</strong> {{ $team->displayName }}</p>
    <p><strong>Short Display Name:</strong> {{ $team->shortDisplayName }}</p>
    <p><strong>Name:</strong> {{ $team->name }}</p>
    <p><strong>Nickname:</strong> {{ $team->nickname }}</p>
    <p><strong>Location:</strong> {{ $team->location }}</p>
    <p><strong>Color:</strong> <span class="inline-block w-6 h-6"
                                     style="background-color: {{ $team->color }}"></span> {{ $team->color }}</p>
    <p><strong>Alternate Color:</strong> <span class="inline-block w-6 h-6"
                                               style="background-color: {{ $team->alternateColor }}"></span> {{ $team->alternateColor }}
    </p>
</div>
