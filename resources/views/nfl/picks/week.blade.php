@php use Carbon\Carbon; @endphp
<x-app-layout>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <h2 class="text-2xl font-semibold mb-6">Select Week</h2>
            <form id="weekForm" method="GET" action="{{ url('/nfl/picks') }}" class="mb-8">
                <div class="mb-4">
                    <label for="week_id" class="block text-sm font-medium text-gray-700">Week:</label>
                    <select name="week_id" id="week_id"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                            onchange="updateUrl(this.value)">
                        <option value="">Select Week</option>
                        @foreach($weeks as $week)
                            <option value="{{ $week->id }}" {{ $week->id == $week_id ? 'selected' : '' }}>
                                Week {{ $week->week_number }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>

            <h2 class="text-2xl font-semibold mb-6">Week {{ $events->first()->week->week_number ?? 'All' }}
                Matchups</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if($events->isEmpty())
                    <p class="text-gray-500">No events found for this week.</p>
                @else
                    @foreach($events as $event)
                        <div class="bg-white shadow-md rounded-lg overflow-hidden relative">
                            <div class="p-6">
                                @if(session('submitted_event_id') == $event->id && session('success'))
                                    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                {{--                                <h5 class="text-xl font-bold mb-4">{{ $event->name }}</h5>--}}
                                <p class="text-xl text-gray-700 mb-2">
                                    <strong>{{ $event->homeTeam->name }}</strong> vs
                                    <strong>{{ $event->awayTeam->name }}</strong>
                                </p>


                                @if(isset($userSubmissions[$event->id]))
                                    @php
                                        $selectedTeam = $userSubmissions[$event->id];
                                        $selectedTeamName = $event->homeTeam->team_id == $selectedTeam ? $event->homeTeam->name : $event->awayTeam->name;
                                    @endphp

                                            <!-- Edit Submission Button -->
                                    <button onclick="toggleSubmissionForm({{ $event->id }})"
                                            class="absolute top-2 right-2 text-gray-400 hover:text-gray-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                             viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15.232 5.232l3.536 3.536M16.707 3.707a1 1 0 00-1.414 0l-9 9a1 1 0 00-.263.53l-.535 3.213a1 1 0 001.263 1.263l3.213-.535a1 1 0 00.53-.263l9-9a1 1 0 000-1.414l-3.536-3.536z"/>
                                        </svg>
                                    </button>

                                    <!-- Message and Submission Form -->
                                    <div id="submission-info-{{ $event->id }}" class="submission-info">
                                        <p class="text-green-600 italic">Your
                                            submission: {{ $selectedTeamName }}
                                            .</p>
                                    </div>

                                    <form id="submission-form-{{ $event->id }}" action="{{ route('nfl.pickWinner') }}"
                                          method="POST" class="submission-form hidden">
                                        @csrf
                                        <input type="hidden" name="event_id" value="{{ $event->id }}">
                                        <div class="mb-4">
                                            <div class="flex items-center mb-2">
                                                <input id="home_team_{{ $event->id }}" name="team_id" type="radio"
                                                       value="{{ $event->home_team_id }}"
                                                       class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300" {{ $selectedTeam == $event->home_team_id ? 'checked' : '' }}>
                                                <label for="home_team_{{ $event->id }}"
                                                       class="ml-3 block text-sm font-medium text-gray-700">
                                                    {{ $event->homeTeam->name }} (Home)
                                                </label>
                                            </div>
                                            <div class="flex items-center">
                                                <input id="away_team_{{ $event->id }}" name="team_id" type="radio"
                                                       value="{{ $event->away_team_id }}"
                                                       class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300" {{ $selectedTeam == $event->away_team_id ? 'checked' : '' }}>
                                                <label for="away_team_{{ $event->id }}"
                                                       class="ml-3 block text-sm font-medium text-gray-700">
                                                    {{ $event->awayTeam->name }} (Away)
                                                </label>
                                            </div>
                                        </div>
                                        <button type="submit"
                                                class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Submit Your Pick
                                        </button>
                                    </form>
                                @else
                                    <!-- New Submission Form -->
                                    <form action="{{ route('nfl.pickWinner') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="event_id" value="{{ $event->id }}">
                                        <div class="mb-4">
                                            <div class="flex items-center mb-2">
                                                <input id="home_team_{{ $event->id }}" name="team_id" type="radio"
                                                       value="{{ $event->home_team_id }}"
                                                       class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                                <label for="home_team_{{ $event->id }}"
                                                       class="ml-3 block text-sm font-medium text-gray-700">
                                                    {{ $event->homeTeam->name }} (Home)
                                                </label>
                                            </div>
                                            <div class="flex items-center">
                                                <input id="away_team_{{ $event->id }}" name="team_id" type="radio"
                                                       value="{{ $event->away_team_id }}"
                                                       class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                                <label for="away_team_{{ $event->id }}"
                                                       class="ml-3 block text-sm font-medium text-gray-700">
                                                    {{ $event->awayTeam->name }} (Away)
                                                </label>
                                            </div>
                                        </div>
                                        <button type="submit"
                                                class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Submit Your Pick
                                        </button>
                                    </form>
                                @endif
                                <p class="text-gray-400 mb-4 mt-2">
                                    {{$event->date}}
                                </p>
                            </div>

                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <script>
        function updateUrl(weekId) {
            const form = document.getElementById('weekForm');
            form.action = `{{ url('/nfl/picks') }}/${weekId}`;
            form.submit();
        }

        function toggleSubmissionForm(eventId) {
            const infoDiv = document.getElementById(`submission-info-${eventId}`);
            const formDiv = document.getElementById(`submission-form-${eventId}`);

            if (infoDiv.classList.contains('hidden')) {
                infoDiv.classList.remove('hidden');
                formDiv.classList.add('hidden');
            } else {
                infoDiv.classList.add('hidden');
                formDiv.classList.remove('hidden');
            }
        }
    </script>
</x-app-layout>
