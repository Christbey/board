<x-app-layout>
        <h1 class="text-2xl font-bold mb-6">NFL Teams</h1>
        <div class="bg-white shadow-md rounded-lg p-6 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Team Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stadium</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($teams as $team)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap" style="color: {{ $team->primary_color }}; background-color: rgba({{ \App\Helpers\ColorHelper::hex2rgb($team->secondary_color ?? '#ffffff') }}, 0.2);">{{ $team->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $team->stadium }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
</x-app-layout>
