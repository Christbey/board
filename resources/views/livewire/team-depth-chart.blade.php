<div class="bg-white p-6 rounded-lg shadow mb-6">
    <h2 class="text-xl font-semibold mb-4">Depth Chart</h2>
    @if($depthChart->isEmpty())
        <p>No depth chart data available for this team.</p>
    @else
        <table class="min-w-full bg-white rounded-lg shadow">
            <thead>
            <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                <th class="py-3 px-6 text-left">Athlete</th>
                <th class="py-3 px-6 text-left">Position</th>
                <th class="py-3 px-6 text-left">Depth</th>
            </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
            @foreach($depthChart as $item)
                <tr class="border-b border-gray-200 hover:bg-gray-100">
                    <td class="py-3 px-6 text-left">{{ $item->athlete->full_name }}</td>
                    <td class="py-3 px-6 text-left">{{ $item->position }}</td>
                    <td class="py-3 px-6 text-left">{{ $item->depth }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</div>
