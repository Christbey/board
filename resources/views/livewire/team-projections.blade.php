<div class="bg-white p-6 rounded-lg shadow mb-6">
    <h2 class="text-xl font-semibold mb-4">Team Projections</h2>
    @if($projections->isEmpty())
        <p class="text-gray-600">No projections data available for this team.</p>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full ">
                <thead>
                <tr class="bg-gray-800 text-white uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">Chance to Win Division</th>
                    <th class="py-3 px-6 text-left">Projected Wins</th>
                    <th class="py-3 px-6 text-left">Projected Losses</th>
                </tr>
                </thead>
                <tbody class="text-gray-700 text-sm font-light">
                @foreach($projections as $projection)
                    <tr class=" hover:bg-gray-100">
                        <td class="py-3 px-6 text-left">{{ number_format($projection->chance_to_win_division, 2) }}%
                        </td>
                        <td class="py-3 px-6 text-left">{{ $projection->projected_wins }}</td>
                        <td class="py-3 px-6 text-left">{{ $projection->projected_losses }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
