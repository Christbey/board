<!-- resources/views/mlb/scores.blade.php -->
<x-app-layout>
    <div class="max-w-3xl py-6 mx-auto text-gray-900">
        <h1 class="text-2xl font-bold mb-6">MLB Scores</h1>
        <div class="grid grid-cols-1 gap-6">
            @foreach ($scores as $score)
                <div class="bg-gray-100 text-gray-600 shadow-md rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <p class="text-sm font-semibold">{{ $score->homeTeam->name }} vs {{ $score->awayTeam->name }}</p>
                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($score->commence_time)->setTimezone('America/Chicago')->format('D, M j, Y g:i A') }} CST</p>
                        </div>
                        <div>
                            @if ($score->completed)
                                <span class="text-gray-500">Completed</span>
                            @elseif ($score->home_team_score !== null && $score->away_team_score !== null)
                                <span class="relative flex h-3 w-3">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                                </span>
                            @else
                                <i class="fas fa-lock text-gray-500"></i>
                            @endif
                        </div>
                    </div>
                    <div class="flex justify-between items-center border-b pb-2 mb-2">
                        <div class="text-sm font-medium">Team</div>
                        <div class="text-sm font-medium text-right">Score</div>
                    </div>
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm font-medium">{{ $score->homeTeam->name }}</p>
                            <p class="text-sm font-medium">{{ $score->awayTeam->name }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm">{{ $score->home_team_score ?? '-' }}</p>
                            <p class="text-sm">{{ $score->away_team_score ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
