<!-- resources/views/livewire/forge-servers.blade.php -->
<div>
    <h1 class="text-xl font-semibold mb-4">Servers</h1>
    @if (count($servers) > 0)
        <ul class="space-y-4">
            @foreach ($servers as $server)
                <li class="p-4 mb-6 bg-white rounded shadow flex items-center justify-between">
                    <div class="">
                        <div class="text-lg font-semibold">{{ $server['name'] }}</div>
                        <div class="text-sm text-gray-600">ID: {{ $server['id'] }}</div>
                        <div class="text-sm text-gray-600">IP: {{ $server['ip_address'] }}</div>
                        <div class="text-sm text-gray-600">Region: {{ $server['region'] }}</div>
                        <div class="text-sm text-gray-600">PHP Version: {{ $server['php_version'] }}</div>
                    </div>
                    @if ($server['is_ready'])
                        <span class="flex h-3 w-3 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                        </span>
                    @endif
                </li>
            @endforeach
        </ul>
    @else
        <p class="text-gray-600">No servers found.</p>
    @endif
</div>
