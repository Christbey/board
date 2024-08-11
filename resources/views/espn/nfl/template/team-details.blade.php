<x-app-layout>
    <body class="bg-gray-100 p-6">
    <div class="container mx-auto">
        <h1 class="text-4xl font-bold mb-6">ESPN NFL Team Details</h1>

        <form method="POST" action="{{ route('filter_team') }}">
            @csrf
            <div class="form-group">
                <label for="team_id">Select Team:</label>
                <select name="team_id" id="team_id" class="form-control">
                    @foreach($teams as $team)
                        <option value="{{ $team->team_id }}" {{ $team->team_id == $selectedTeamId ? 'selected' : '' }}>
                            {{ $team->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>

        <h3 class="text-2xl font-semibold mb-4">Events:</h3>
        <livewire:team-events :events="$teamData['events']['items'] ?? []"/>

      
    @livewireScripts
    </body>
</x-app-layout>
