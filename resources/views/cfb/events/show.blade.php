@php use Carbon\Carbon; @endphp

<x-app-layout>

    <div class="container mx-auto p-6">
        <!-- Back Button -->
        <a href="{{ url()->previous() }}"
           class="inline-flex items-center px-4 py-2 mb-4 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-700">
            ← Back
        </a>
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold">{{ $game->awayTeam->school }} vs. {{ $game->homeTeam->school }}</h2>
            <p>Date: {{ Carbon::parse($game->start_date)->format('m/d/Y') }}</p>
            <p>Location: {{ $game->venue ?? 'Unknown Venue' }}</p>

            <h3 class="text-lg font-semibold mb-4">Team Ratings Comparison for {{ $year }}</h3>

            <!-- Chart Container -->
            <div class="mb-6">
                <canvas id="ratingsComparisonChart" aria-label="Team Ratings Comparison Chart" role="img"></canvas>
            </div>

            <!-- Ratings Summary (fallback for no JS or additional details) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                <div>
                    <h4 class="text-md font-semibold">{{ $game->awayTeam->school }} Ratings</h4>
                    <p>Pregame Elo Rating: {{ $pregameData->away_pregame_elo ?? 'N/A' }}</p>
                    <p>FPI Rating: {{ $awayFpiRating->fpi ?? 'N/A' }}</p>
                    <!-- Additional away team ratings here -->


                    <h4 class="text-md font-semibold mt-4"> Away Advanced Stats</h4>
                    <p>Plays: {{ $awayAdvStats->offense_plays ?? 'N/A' }}</p>
                    <p>PPA: {{ $awayAdvStats->offense_ppa ?? 'N/A' }}</p>
                    <p>Success Rate: {{ $awayAdvStats->offense_success_rate ?? 'N/A' }}</p>
                    <p>Explosiveness: {{ $awayAdvStats->offense_explosiveness ?? 'N/A' }}</p>
                    <!-- Add more stats as needed -->

                </div>
                <div>
                    <h4 class="text-md font-semibold">{{ $game->homeTeam->school }} Ratings</h4>
                    <p>Pregame Elo Rating: {{ $pregameData->home_pregame_elo ?? 'N/A' }}</p>
                    <p>FPI Rating: {{ $homeFpiRating->fpi ?? 'N/A' }}</p>
                    <!-- Additional home team ratings here -->

                    <h4 class="text-md font-semibold mt-4">Home Advanced Stats</h4>
                    <p>Plays: {{ $homeAdvStats->offense_plays ?? 'N/A' }}</p>
                    <p>PPA: {{ $homeAdvStats->offense_ppa ?? 'N/A' }}</p>
                    <p>Success Rate: {{ $homeAdvStats->offense_success_rate ?? 'N/A' }}</p>
                    <p>Explosiveness: {{ $homeAdvStats->offense_explosiveness ?? 'N/A' }}</p>
                    <!-- Add more stats as needed -->
                </div>
            </div>
        </div>
    </div>

    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Check if pregameData exists and set default values to 56 if not
        const pregameData = @json($pregameData);
        const homeWinProb = pregameData ? pregameData.home_win_prob * 100 : 56;
        const awayWinProb = 100 - homeWinProb;

        // Set the spread to 3.5 if it's null
        const spread = pregameData ? pregameData.spread ?? 3.5 : 3.5;

        // Data for the charts
        const labels = ['Elo', 'FPI', 'Strength of Record', 'Strength of Schedule', 'Spread', 'Home Win Probability'];

        const homeRatingsData = [
            {{ $game->home_pregame_elo / 10 ?? 56 }},
            {{ $homeFpiRating->fpi ?? 56 }},
            {{ $homeFpiRating->strength_of_record ?? 56 }},
            {{ $homeFpiRating->strength_of_schedule ?? 56 }},
            spread,
            homeWinProb
        ];

        const awayRatingsData = [
            {{ $game->away_pregame_elo / 10 ?? 56 }},
            {{ $awayFpiRating->fpi ?? 56 }},
            {{ $awayFpiRating->strength_of_record ?? 56 }},
            {{ $awayFpiRating->strength_of_schedule ?? 56 }},
            -spread, // Negative spread for away team
            awayWinProb
        ];

        // Configuration for the comparison chart
        const ratingsComparisonChartConfig = {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: '{{ $game->awayTeam->school }} Ratings',
                        data: awayRatingsData,
                        backgroundColor: 'rgba(255, 99, 132, 0.5)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    },
                    {
                        label: '{{ $game->homeTeam->school }} Ratings',
                        data: homeRatingsData,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        suggestedMax: 100
                    }
                },
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                let value = context.raw !== null ? context.raw : 'N/A';
                                return `${context.dataset.label}: ${value}`;
                            }
                        }
                    }
                }
            }
        };

        // Render the comparison chart
        const ratingsComparisonChart = new Chart(
            document.getElementById('ratingsComparisonChart'),
            ratingsComparisonChartConfig
        );
    </script>
</x-app-layout>
