<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <h2 class="text-2xl font-semibold mb-6">Your Picks for Week {{ $weekNumber }}</h2>

            <!-- Week Filter -->
            <form method="GET" action="{{ route('espn.picks.submissions', $weekId) }}" class="mb-6">
                <label for="week_id" class="block text-sm font-medium text-gray-700">Select Week:</label>
                <select name="week_id" id="week_id"
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                        onchange="this.form.submit()">
                    @foreach($weeks as $week)
                        <option value="{{ $week->id }}" {{ $week->id == $weekId ? 'selected' : '' }}>
                            Week {{ $week->week_number }}
                        </option>

                    @endforeach

                </select>

            </form>


            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($userSubmissions as $submission)
                    <div class="bg-white shadow-md rounded-lg p-4">
                        <p class="text-xl font-semibold">{{ $submission->event->homeTeam->name }}
                            vs {{ $submission->event->awayTeam->name }}</p>
                        <p class="text-sm text-gray-600">Your Pick: <strong>{{ $submission->team->name }}</strong></p>
                        <p class="text-sm text-gray-600">Winning Team:
                            <strong>
                                @if($submission->event->home_team_score > $submission->event->away_team_score)
                                    {{ $submission->event->homeTeam->name }}
                                @elseif($submission->event->away_team_score > $submission->event->home_team_score)
                                    {{ $submission->event->awayTeam->name }}
                                @else
                                    TBD
                                @endif
                            </strong>
                        </p>
                        <p class="text-sm text-gray-600">
                            Percentage of Users Picking {{ $submission->team->name }}:
                            <strong>{{ $teamSelectionPercentages[$submission->event_id][$submission->team_id] ?? 0 }}
                                %</strong>
                        </p>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                <h3 class="text-xl font-semibold mb-4">Week Statistics</h3>
                <p class="text-lg">Correct Picks This Week: <strong>{{ $correctPicksThisWeek }}</strong></p>
                <p class="text-lg">Correct Picks Total: <strong>{{ $correctPicksTotal }}</strong></p>
                <p class="text-lg">Rank This Week: <strong>{{ $rankThisWeek }}</strong></p>
                <p class="text-lg">Rank Total: <strong>{{ $rankTotal }}</strong></p>
            </div>
        </div>
    </div>
</x-app-layout>
