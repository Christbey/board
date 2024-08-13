<x-app-layout>
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-semibold text-gray-800 mb-6">College Football Teams (FBS)</h1>

        <form method="GET" action="{{ route('cfb.teams.index') }}" class="mb-6">
            <div class="flex items-center space-x-4">
                <label for="conference" class="block text-sm font-medium text-gray-700">Filter by Conference:</label>
                <select name="conference" id="conference"
                        class="block w-64 px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        onchange="this.form.submit()">
                    <option value="">All Conferences</option>
                    @foreach($conferences as $conf)
                        <option value="{{ $conf->name }}" {{ $conferenceName == $conf->name ? 'selected' : '' }}>
                            {{ $conf->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>

        <div class="overflow-x-auto bg-white rounded-lg shadow-md">
            <table class="min-w-full bg-white">
                <thead>
                <tr class="bg-gray-100 border-b">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">School
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mascot
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Abbreviation
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($teams as $team)
                    <tr class="cursor-pointer hover:bg-gray-100"
                        onclick="window.location='{{ route('cfb.teams.show', $team->id) }}'">
                        <td class="px-6 py-4 whitespace-nowrap">{{ $team->school }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $team->mascot }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $team->abbreviation }}</td>
                    </tr>

                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
