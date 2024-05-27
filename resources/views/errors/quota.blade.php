<!-- resources/views/errors/quota.blade.php -->

<x-app-layout>
    <div class="max-w-3xl py-6 mx-auto text-gray-900">
        <h1 class="text-2xl font-bold mb-6">Error</h1>
        <div class="bg-red-100 text-red-700 shadow-md rounded-lg p-6">
            <p class="text-lg font-semibold mb-4">An error occurred while fetching the odds.</p>
            <p class="text-md">{{ $message }}</p>
            <p class="text-md mt-4"><a href="{{ $details_url }}" class="text-blue-500 underline">Learn more</a></p>
        </div>
    </div>
</x-app-layout>
