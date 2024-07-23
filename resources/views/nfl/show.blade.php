<x-app-layout>
    <div class="container mx-auto p-4 sm:p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold">{{ $team->name }}</h1>
            <a href="{{ url()->previous() }}" class="text-blue-600 hover:text-blue-800">
                ← Back
            </a>
        </div>
        <div class="bg-white shadow-md rounded-lg p-4 sm:p-6">
            <p class="text-lg mb-4"><strong>Stadium:</strong> {{ $team->stadium }}</p>
            <p class="text-lg mb-4"><strong>Expected Wins:</strong> {{ $expectedWins }}
            </p>
            <!-- Add more details as needed -->
        </div>
    </div>
</x-app-layout>
