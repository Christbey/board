<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-4">
                MLB Teams
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($teams as $team)
                    <div class="p-4 bg-gray-100 rounded-lg shadow-md">
                        <h3 class="font-bold text-lg">{{ $team->name }}</h3>
                        <p>Abbreviation: {{ $team->abbreviation }}</p>
                        <p>Conference: {{ $team->conference }}</p>
                        <p>Division: {{ $team->division }}</p>
                        <p>Mascot: {{ $team->team_mascot }}</p>
                        <p>Location: {{ $team->location }}</p>
                        <p>Stadium: {{ $team->stadium }}</p>
                        <p>City: {{ $team->city }}</p>
                        <p>State: {{ $team->state }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
