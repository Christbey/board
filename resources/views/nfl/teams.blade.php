@php use App\Helpers\ColorHelper; @endphp
<x-app-layout>
    <h1 class="text-2xl font-bold mb-6">NFL Teams</h1>
    <div class="bg-white shadow-md rounded-lg p-6 overflow-x-auto"
         x-data="{ showModal: false, selectedTeam: null, nextOpponents: [] }">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Team Name
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expected
                    Wins
                </th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            @foreach ($teams as $team)
                <tr @click="showModal = true; selectedTeam = {
                        id: {{ $team->id }},
                        name: '{{ $team->name }}',
                        stadium: '{{ $team->stadium }}',
                        expectedWins: '{{ $expectedWins[$team->id] ?? 'N/A' }}'
                    }; nextOpponents = {{ $nextOpponents[$team->id]->toJson() }}" class="cursor-pointer">
                    <td class="px-6 py-4 whitespace-nowrap"
                        style="color: {{ $team->primary_color }}; background-color: rgba({{ ColorHelper::hex2rgb($team->secondary_color ?? '#ffffff') }}, 0.2);">{{ $team->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $expectedWins[$team->id] ?? 'N/A' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <!-- Modal -->
        <div x-show="showModal" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
             aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showModal" x-cloak class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                     aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="showModal" x-cloak
                     class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <!-- Icon can be placed here -->
                            <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title"
                                x-text="selectedTeam.name"></h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500" x-text="'Stadium: ' + selectedTeam.stadium"></p>
                                <p class="text-sm text-gray-500"
                                   x-text="'Expected Wins: ' + selectedTeam.expectedWins"></p>
                                <!-- Next Opponents Table -->
                                <div class="mt-4">
                                    <h4 class="text-md font-semibold text-gray-900">Next 3 Opponents:</h4>
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Opponent
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Date
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                        <template x-for="opponent in nextOpponents" :key="opponent.game_date">
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap"
                                                    x-text="(opponent.team_id_home == selectedTeam.id ? opponent.away : opponent.home)"></td>
                                                <td class="px-6 py-4 whitespace-nowrap"
                                                    x-text="new Date(opponent.game_date).toLocaleDateString()"></td>
                                            </tr>
                                        </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <button @click="showModal = false" type="button"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

