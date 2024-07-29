<ul class="list-none mb-6">
    @if($attendance)
        @foreach($attendance as $attend)
            <x-list-item>
                {{ json_encode($attend, JSON_PRETTY_PRINT) }}
            </x-list-item>
        @endforeach
    @else
        <x-list-item>No attendance data available.</x-list-item>
    @endif
</ul>
