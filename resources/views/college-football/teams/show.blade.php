@php use Carbon\Carbon; @endphp
@php
    function darkenHexColor($hex, $factor = 20) {
        // Remove "#" if present
        $hex = str_replace("#", "", $hex);

        // Convert to RGB
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        // Darken the color by the factor
        $r = max(0, $r - $factor);
        $g = max(0, $g - $factor);
        $b = max(0, $b - $factor);

        // Return the darkened color in hex format
        return sprintf("#%02x%02x%02x", $r, $g, $b);
    }

    $teamColor = $teamData->color;
    $darkenedBorderColor = darkenHexColor($teamColor, 30); // Adjust the factor as needed
@endphp

<x-app-layout>
    <div class="container mx-auto p-6">
        <a href="{{ url()->previous() }}"
           class="inline-flex items-center px-4 py-2 mb-4 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-700">
            ← Back
        </a>
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <!-- Team Information -->
            <div class="mb-6">
                <h2 class="text-2xl font-semibold">{{ $teamData->school }}</h2>
                <p class="text-gray-700">Mascot: {{ $teamData->mascot }}</p>
                <p class="text-gray-700">Abbreviation: {{ $teamData->abbreviation }}</p>
                <p class="text-gray-700">Conference: {{ $teamData->conference->name }}</p>
                <p class="text-gray-700">Location: {{ $teamData->city }}, {{ $teamData->state }}</p>
                <p class="text-gray-700">Stadium: {{ $teamData->venue_name }} (Capacity: {{ $teamData->capacity }})</p>
                <p class="text-gray-700">Elo Rating: {{ $eloRating->elo ?? 'N/A' }}</p>
                <p class="text-gray-700">FPI Rating: {{ $fpiRating->fpi ?? 'N/A' }}</p>
            </div>

            <!-- Talent Data Chart -->
            @if($talentData)
                <div class="mb-6">
                    <canvas id="talentChart" aria-label="Talent Ranking Comparison Chart" role="img"></canvas>
                </div>
            @else
                <p class="text-gray-500">No talent data available for {{ $year }}.</p>
            @endif
            <!-- FPI Comparison Chart -->
            @if($fpiRating)
                <canvas id="fpiChart" aria-label="FPI Rating Comparison Chart" role="img"></canvas>
            @else
                <p>No FPI data available for {{ $year }}.</p>
            @endif

            <!-- Advanced Season Stats -->
            @if($advStats)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold">Advanced Season Stats for {{ $year }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 bg-gray-100 rounded-lg">
                            <h4 class="text-md font-semibold mb-2">Offense Stats</h4>
                            <p>Total Plays: {{ $advStats->offense_plays ?? 'N/A' }}</p>
                            <p>Drives: {{ $advStats->offense_drives ?? 'N/A' }}</p>
                            <p>PPA: {{ $advStats->offense_ppa ?? 'N/A' }}</p>
                            <p>Total PPA: {{ $advStats->offense_total_ppa ?? 'N/A' }}</p>
                            <p>Success Rate: {{ $advStats->offense_success_rate ?? 'N/A' }}</p>
                            <p>Explosiveness: {{ $advStats->offense_explosiveness ?? 'N/A' }}</p>
                        </div>
                        <div class="p-4 bg-gray-100 rounded-lg">
                            <h4 class="text-md font-semibold mb-2">Rushing Stats</h4>
                            <p>Rushing Plays Rate: {{ $advStats->offense_rushing_plays_rate ?? 'N/A' }}</p>
                            <p>Rushing Plays PPA: {{ $advStats->offense_rushing_plays_ppa ?? 'N/A' }}</p>
                            <p>Total Rushing Plays PPA: {{ $advStats->offense_rushing_plays_total_ppa ?? 'N/A' }}</p>
                            <p>Rushing Plays Success
                                Rate: {{ $advStats->offense_rushing_plays_success_rate ?? 'N/A' }}</p>
                            <p>Rushing Plays
                                Explosiveness: {{ $advStats->offense_rushing_plays_explosiveness ?? 'N/A' }}</p>
                            <p>Rushing Plays Power Success: {{ $advStats->offense_power_success ?? 'N/A' }}</p>
                            <p>Rushing Plays Stuff Rate: {{ $advStats->offense_stuff_rate ?? 'N/A' }}</p>
                        </div>

                        <div class="p-4 bg-gray-100 rounded-lg mt-4">
                            <h4 class="text-md font-semibold mb-2">Passing Stats</h4>
                            <p>Passing Plays Rate: {{ $advStats->offense_passing_plays_rate ?? 'N/A' }}</p>
                            <p>Passing Plays PPA: {{ $advStats->offense_passing_plays_ppa ?? 'N/A' }}</p>
                            <p>Total Passing Plays PPA: {{ $advStats->offense_passing_plays_total_ppa ?? 'N/A' }}</p>
                            <p>Passing Plays Success
                                Rate: {{ $advStats->offense_passing_plays_success_rate ?? 'N/A' }}</p>
                            <p>Passing Plays
                                Explosiveness: {{ $advStats->offense_passing_plays_explosiveness ?? 'N/A' }}</p>
                        </div>

                        <div class="p-4 bg-gray-100 rounded-lg mt-4">
                            <h4 class="text-md font-semibold mb-2">Standard Downs</h4>
                            <p>Standard Downs Rate: {{ $advStats->offense_standard_downs_rate ?? 'N/A' }}</p>
                            <p>Standard Downs PPA: {{ $advStats->offense_standard_downs_ppa ?? 'N/A' }}</p>
                            <p>Standard Downs Success
                                Rate: {{ $advStats->offense_standard_downs_success_rate ?? 'N/A' }}</p>
                            <p>Standard Downs
                                Explosiveness: {{ $advStats->offense_standard_downs_explosiveness ?? 'N/A' }}</p>
                        </div>

                        <div class="p-4 bg-gray-100 rounded-lg mt-4">
                            <h4 class="text-md font-semibold mb-2">Passing Downs</h4>
                            <p>Passing Downs Rate: {{ $advStats->offense_passing_downs_rate ?? 'N/A' }}</p>
                            <p>Passing Downs PPA: {{ $advStats->offense_passing_downs_ppa ?? 'N/A' }}</p>
                            <p>Passing Downs Success
                                Rate: {{ $advStats->offense_passing_downs_success_rate ?? 'N/A' }}</p>
                            <p>Passing Downs
                                Explosiveness: {{ $advStats->offense_passing_downs_explosiveness ?? 'N/A' }}</p>
                        </div>

                        <div class="p-4 bg-gray-100 rounded-lg mt-4">
                            <h4 class="text-md font-semibold mb-2">Field Position</h4>
                            <p>Average Start
                                Position: {{ $advStats->offense_field_position_average_start ?? 'N/A' }}</p>
                            <p>Average Predicted
                                Points: {{ $advStats->offense_field_position_average_predicted_points ?? 'N/A' }}</p>
                        </div>

                        <div class="p-4 bg-gray-100 rounded-lg mt-4">
                            <h4 class="text-md font-semibold mb-2">Havoc Rate</h4>
                            <p>Total Havoc Rate: {{ $advStats->offense_havoc_total ?? 'N/A' }}</p>
                            <p>Havoc Rate (Front Seven): {{ $advStats->offense_havoc_front_seven ?? 'N/A' }}</p>
                            <p>Havoc Rate (Defensive Backs): {{ $advStats->offense_havoc_db ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            @else
                <p class="text-gray-500">No advanced season stats available for {{ $year }}.</p>
            @endif
        </div>

        <!-- Year Filter -->
        <form method="GET" action="{{ route('collegeFootball.teams.show', $teamData->id) }}" class="mb-6">
            <div class="flex items-center space-x-4">
                <label for="year" class="block text-sm font-medium text-gray-700">Select Year:</label>
                <select name="year" id="year"
                        class="block w-64 px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        onchange="this.form.submit()">
                    @foreach(range(date('Y'), 2000) as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
        </form>

        <!-- Games Table -->
        <div class="overflow-x-auto bg-white rounded-lg shadow-md">
            <h3 class="text-lg font-semibold mb-4">Games</h3>
            <table class="min-w-full bg-white">
                <thead>
                <tr class="bg-gray-100 border-b">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Opponent
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Location
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Result
                    </th>
                </tr>
                </thead>
                <tbody>
                @forelse($games as $game)
                    <tr class="cursor-pointer hover:bg-gray-100"
                        onclick="window.location='{{ route('collegeFootball.events.show', $game->id) }}'">
                        <td class="px-6 py-4 whitespace-nowrap">{{ Carbon::parse($game->start_date)->format('m/d') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $game->home_id == $teamData->id ? 'vs. ' . ($game->awayTeam->school ?? 'Unknown Team') : '@ ' . ($game->homeTeam->school ?? 'Unknown Team') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $game->venue ?? 'Unknown Venue' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $game->result ?? 'TBD' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">No games available for this team.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Include Chart.js and Talent Chart Script -->
    @if($talentData)
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const ctx = document.getElementById('talentChart').getContext('2d');

                const talentChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Talent'], // These labels will be hidden
                        datasets: [
                            {
                                axis: 'y', // Set axis to 'y' for horizontal bars
                                label: '{{ $teamData->school }} Talent',
                                data: [{{ $talentData->talent ?? 0 }}],
                                backgroundColor: '{{ $teamData->color }}',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1,
                            },
                            {
                                axis: 'y', // Set axis to 'y' for horizontal bars
                                label: 'League Average Talent',
                                data: [{{ $averageTalent ?? 0 }}],
                                backgroundColor: 'rgba(211, 211, 211, 0.6)',
                                borderColor: 'rgba(211, 211, 211, 1)',
                                borderWidth: 1,
                            }
                        ]
                    },
                    options: {
                        indexAxis: 'y', // Ensures the bars are horizontal
                        scales: {
                            x: {
                                beginAtZero: true,
                                suggestedMax: Math.max({{ $talentData->talent ?? 0 }}, {{ $averageTalent ?? 0 }}) * 1.2
                            },
                            y: {
                                ticks: {
                                    display: false // This hides the labels on the y-axis
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        return `${context.dataset.label}: ${context.raw}`;
                                    }
                                }
                            },
                            title: {
                                display: true,
                                text: 'Talent Ranking Comparison for {{ $teamData->school }} in {{ $year }}',
                                font: {
                                    size: 18
                                }
                            }
                        }
                    }
                });
            });
        </script>

        <!-- Set canvas size -->
        <style>
            #talentChart {
                max-height: 300px; /* Adjust height */
            }
        </style>

        <!-- Canvas for the chart -->
    @endif
    @if($fpiRating)
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const ctx = document.getElementById('fpiChart').getContext('2d');

                const fpiChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Team FPI', 'Conf. Avg. FPI'], // These labels will be hidden
                        datasets: [{
                            label: 'FPI Rating for {{ $teamData->school }} vs Conference Average',
                            data: [{{ $fpiRating->fpi ?? 0 }}, {{ $averageConferenceFpi ?? 0 }}],
                            backgroundColor: [
                                '{{ $teamData->color }}',
                                'rgba(211, 211, 211, 0.6)'
                            ],
                            borderColor: [
                                '{{ $darkenedBorderColor }}',
                                'rgba(211, 211, 211, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        indexAxis: 'y', // This makes the chart horizontal
                        scales: {
                            x: {
                                beginAtZero: true,
                                suggestedMax: Math.max({{ $fpiRating->fpi ?? 0 }}, {{ $averageConferenceFpi ?? 0 }}) * 1.1
                            },
                            y: {
                                ticks: {
                                    display: false // This hides the labels on the y-axis
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        return `${context.dataset.label}: ${context.raw}`;
                                    }
                                }
                            },
                            title: {
                                display: true,
                                text: 'FPI Rating Comparison for {{ $teamData->school }} in {{ $year }}',
                                font: {
                                    size: 18
                                }
                            }
                        }
                    }
                });
            });
        </script>
        <!-- Set canvas size -->
        <style>
            #fpiChart {
                max-height: 300px; /* Adjust height */
            }
        </style>
    @endif
</x-app-layout>
