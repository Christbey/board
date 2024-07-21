<x-app-layout>

    <div class="container mx-auto p-4 sm:p-6">
        <h1 class="text-2xl sm:text-3xl font-bold mb-6">{{ $team->name }}</h1>
        <div class="bg-white shadow-md rounded-lg p-4 sm:p-6">
            <p class="text-lg mb-4"><strong>Stadium:</strong> {{ $team->stadium }}</p>
            <p class="text-lg mb-4"><strong>Expected Wins:</strong> {{ $team->expected_wins }}</p>
            <!-- Add more details as needed -->
        </div>
    </div>
</x-app-layout>