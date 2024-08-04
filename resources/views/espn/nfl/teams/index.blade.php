<!-- resources/views/espn/nfl/teams/index.blade.php -->

<x-app-layout>
    <div class="container mx-auto py-8">
        <h1 class="text-2xl font-semibold mb-6">NFL Teams</h1>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-lg shadow">
                <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">ID</th>
                    <th class="py-3 px-6 text-left">UID</th>
                    <th class="py-3 px-6 text-left">Slug</th>
                    <th class="py-3 px-6 text-left">Abbreviation</th>
                    <th class="py-3 px-6 text-left">Display Name</th>
                    <th class="py-3 px-6 text-left">Short Display Name</th>
                    <th class="py-3 px-6 text-left">Name</th>
                    <th class="py-3 px-6 text-left">Nickname</th>
                    <th class="py-3 px-6 text-left">Location</th>
                    <th class="py-3 px-6 text-left">Color</th>
                    <th class="py-3 px-6 text-left">Alternate Color</th>
                </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                @foreach($teams as $team)
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left whitespace-nowrap">
                            <a href="{{ route('espn.nfl.teams.show', $team->team_id) }}"
                               class="text-blue-500 hover:underline">
                                {{ $team->team_id }}
                            </a>
                        </td>
                        <td class="py-3 px-6 text-left">{{ $team->uid }}</td>
                        <td class="py-3 px-6 text-left">{{ $team->slug }}</td>
                        <td class="py-3 px-6 text-left">{{ $team->abbreviation }}</td>
                        <td class="py-3 px-6 text-left">{{ $team->displayName }}</td>
                        <td class="py-3 px-6 text-left">{{ $team->shortDisplayName }}</td>
                        <td class="py-3 px-6 text-left">{{ $team->name }}</td>
                        <td class="py-3 px-6 text-left">{{ $team->nickname }}</td>
                        <td class="py-3 px-6 text-left">{{ $team->location }}</td>
                        <td class="py-3 px-6 text-left">{{ $team->color }}</td>
                        <td class="py-3 px-6 text-left">{{ $team->alternateColor }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
