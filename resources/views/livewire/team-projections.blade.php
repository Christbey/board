<div class="bg-white p-6 rounded-lg shadow mb-6">
    <h2 class="text-xl font-semibold mb-4">Team Projections</h2>
    @if($projections->isEmpty())
        <p class="text-gray-600">No projections data available for this team.</p>
    @else
        <div class="overflow-x-auto mb-4">
            <table class="min-w-full">
                <thead>
                <tr class="bg-gray-800 text-white uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">Chance to Win Division</th>
                    <th class="py-3 px-6 text-left">Projected Wins</th>
                    <th class="py-3 px-6 text-left">Projected Losses</th>
                </tr>
                </thead>
                <tbody class="text-gray-700 text-sm font-light">
                @foreach($projections as $projection)
                    <tr class="hover:bg-gray-100">
                        <td class="py-3 px-6 text-left">{{ number_format($projection->chance_to_win_division, 2) }}%
                        </td>
                        <td class="py-3 px-6 text-left">{{ $projection->projected_wins }}</td>
                        <td class="py-3 px-6 text-left">{{ $projection->projected_losses }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <!-- Chart Container -->
        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <h2 class="text-xl font-semibold mb-4">Projected Wins vs. League Average</h2>
            <div class="mb-6">
                <canvas id="projectedWinsChart" aria-label="Projected Wins vs. League Average Chart"
                        role="img"></canvas>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const ctx = document.getElementById('projectedWinsChart').getContext('2d');

                    const projectedWinsChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: ['Projected Wins'], // Label for the x-axis
                            datasets: [
                                {
                                    label: '{{ $teamId }} Projected Wins',
                                    data: [{{ $projections->first()->projected_wins ?? 0 }}],
                                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                                    borderColor: 'rgba(54, 162, 235, 1)',
                                    borderWidth: 1,
                                },
                                {
                                    label: 'League Average Wins',
                                    data: [{{ $leagueAverageWins ?? 0 }}],
                                    backgroundColor: 'rgba(211, 211, 211, 0.6)',
                                    borderColor: 'rgba(211, 211, 211, 1)',
                                    borderWidth: 1,
                                }
                            ]
                        },
                        options: {
                            indexAxis: 'y', // Makes the chart horizontal
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    suggestedMax: Math.max({{ $projections->first()->projected_wins ?? 0 }}, {{ $leagueAverageWins ?? 0 }}) * 1.2
                                },
                                y: {
                                    ticks: {
                                        display: true // Display y-axis labels
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
                                    text: 'Projected Wins vs. League Average',
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
                #projectedWinsChart {
                    max-height: 300px; /* Adjust height */
                }
            </style>
        </div>
    @endif
</div>
