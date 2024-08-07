<div class="bg-white p-6 rounded-lg shadow mb-8">
    <h2 class="text-2xl font-semibold mb-6">Future Predictions</h2>
    <div class="mb-4">
        <select wire:model="filterProviderName" class="p-2 border border-gray-300 rounded-lg w-full">
            <option value="">Select Provider Name</option>
            @foreach($allProviderNames as $providerName)
                <option value="{{ $providerName }}">{{ $providerName }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-4">
        <button wire:click="fetchFutures" class="p-2 bg-blue-500 text-white rounded-lg">Load Results</button>
    </div>
    @if(empty($futures))
        <p class="text-gray-600">No future predictions available for this team.</p>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-lg shadow-md">
                <thead>
                <tr class="bg-gray-800 text-white uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">Future ID</th>
                    <th class="py-3 px-6 text-left">Name</th>
                    <th class="py-3 px-6 text-left">Display Name</th>
                    <th class="py-3 px-6 text-left">Provider ID</th>
                    <th class="py-3 px-6 text-left">Provider Name</th>
                    <th class="py-3 px-6 text-left">Athlete ID</th>
                    <th class="py-3 px-6 text-left">Team ID</th>
                    <th class="py-3 px-6 text-left">Value</th>
                </tr>
                </thead>
                <tbody class="text-gray-700 text-sm font-light">
                @foreach($futures as $future)
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left">{{ $future->future_id }}</td>
                        <td class="py-3 px-6 text-left">{{ $future->name }}</td>
                        <td class="py-3 px-6 text-left">{{ $future->display_name }}</td>
                        <td class="py-3 px-6 text-left">{{ $future->provider_id }}</td>
                        <td class="py-3 px-6 text-left">{{ $future->provider_name }}</td>
                        <td class="py-3 px-6 text-left">{{ $future->athlete_id }}</td>
                        <td class="py-3 px-6 text-left">{{ $future->team_id }}</td>
                        <td class="py-3 px-6 text-left">{{ $future->value }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
