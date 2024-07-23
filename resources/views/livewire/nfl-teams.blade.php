@php
    use App\Helpers\ColorHelper;
    use Carbon\Carbon;
    use App\Helpers\FormatHelper;
@endphp

<div x-data="teamData()" class="container mx-auto p-4 sm:p-6">
    <h1 class="text-2xl sm:text-3xl font-bold mb-6 text-center">NFL Teams</h1>
    <div class="bg-white shadow-md rounded-lg p-4 sm:p-6">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
            <tr>
                <th class="px-3 py-2 sm:px-6 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Team Name
                </th>
                <th class="px-3 py-2 sm:px-6 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Expected Wins
                </th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            @foreach ($teams as $team)
                <tr @click="openModal({{ $team }})" class="cursor-pointer hover:bg-gray-100">
                    <td class="px-3 py-2 sm:px-6 sm:py-4 whitespace-nowrap"
                        style="color: {{ $team->primary_color ?? '#000000' }}; background-color: rgba({{ ColorHelper::hex2rgb($team->secondary_color ?? '#ffffff') }}, 0.2);">{{ $team->name }}</td>
                    <td class="px-3 py-2 sm:px-6 sm:py-4 whitespace-nowrap">{{ $expectedWins[$team->id] ?? 'N/A' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- Custom Modal -->
    <div x-show="showModal" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
         aria-modal="true" @keydown.escape.window="showModal = false">
        <div class="flex items-center justify-center min-h-screen text-center sm:block sm:p-0">
            <div x-show="showModal" x-cloak class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                 aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="showModal" x-cloak
                 class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:px-6 sm:pt-6 sm:pb-5">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title"
                        x-text="selectedTeam ? selectedTeam.name : ''"></h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="mt-2" x-show="selectedTeam">
                    <p class="text-sm text-gray-500" x-text="'Stadium: ' + (selectedTeam.stadium ?? 'N/A')"></p>
                    <p class="text-sm text-gray-500"
                       x-text="'Expected Wins: ' + (expectedWins[selectedTeam.id] ?? 'N/A')"></p>
                    <!-- Next Opponents Table -->
                    <div class="mt-4">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Opponent
                                </th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Spread
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="opponent in nextOpponents[selectedTeam.id]" :key="opponent.id">
                                <tr>
                                    <td class="px-3 py-2 whitespace-nowrap"
                                        x-text="opponent ? FormatHelper.formatOpponent(opponent, selectedTeam.id) : ''"></td>
                                    <td class="px-3 py-2 whitespace-nowrap"
                                        x-text="opponent ? Carbon.parse(opponent.game_date ?? now()).toFormattedDateString() : ''"></td>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <span x-show="opponent.team_id_home == selectedTeam.id"
                                              x-text="opponent.odds ? opponent.odds.spread_home_point : 'N/A'"></span>
                                        <span x-show="opponent.team_id_away == selectedTeam.id"
                                              x-text="opponent.odds ? opponent.odds.spread_away_point : 'N/A'"></span>
                                    </td>
                                </tr>
                            </template>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <a :href="selectedTeam ? `/nfl/teams/${selectedTeam.id}` : '#'"
                       class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        View Team
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function teamData() {
        return {
            showModal: false,
            selectedTeam: null,
            expectedWins: @json($expectedWins),
            nextOpponents: @json($nextOpponents),
            openModal(team) {
                this.selectedTeam = team;
                this.showModal = true;
            }
        }
    }
</script>
