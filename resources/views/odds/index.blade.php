<!-- resources/views/odds/index.blade.php -->

<x-app-layout>
    <div class="py-6 mx-3 text-gray-900 content-center">
        <h1 class="text-2xl font-bold mb-6">Select Sport</h1>
        <div class="bg-white shadow-md rounded-lg p-6">
            <form method="GET" action="{{ route('odds.fetch') }}" id="oddsForm">
                <div class="mb-4">
                    <label for="sport" class="block text-sm font-medium text-gray-700">Sport</label>
                    <select name="sport" id="sport" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        @foreach ($sports as $sport)
                            <option value="{{ $sport['key'] }}">{{ $sport['title'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="submit" class="px-4 py-2 bg-indigo-500 text-white rounded-md">Fetch Odds</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
