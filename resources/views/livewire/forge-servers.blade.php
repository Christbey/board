<!-- resources/views/livewire/forge-servers.blade.php -->
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-6 text-center">Servers</h1>
    @if (count($servers) > 0)
        <ul class="space-y-4">
            @foreach ($servers as $server)
                <li class="p-6 bg-white rounded-lg shadow-lg flex items-center justify-between cursor-pointer hover:bg-gray-50 transition duration-150" wire:click="fetchSites({{ $server['id'] }})">
                    <div>
                        <div class="text-xl font-bold text-gray-800">{{ $server['name'] }}</div>
                        <div class="text-sm text-gray-600 mt-1">ID: {{ $server['id'] }}</div>
                        <div class="text-sm text-gray-600 mt-1">IP: {{ $server['ip_address'] }}</div>
                        <div class="text-sm text-gray-600 mt-1">Region: {{ $server['region'] }}</div>
                        <div class="text-sm text-gray-600 mt-1">PHP Version: {{ $server['php_version'] }}</div>
                    </div>
                    @if ($server['is_ready'])
                        <div class="flex items-center">
                            <span class="relative flex h-4 w-4">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-4 w-4 bg-green-500"></span>
                            </span>
                            <span class="ml-2 text-sm text-gray-600">Ready</span>
                        </div>
                    @endif
                </li>
                @if ($selectedServer === $server['id'])
                    <ul class="ml-8 mt-4 space-y-2">
                        @foreach ($sites as $site)
                            <li class="p-4 bg-gray-50 rounded shadow">
                                <div class="text-md font-medium text-gray-800">{{ $site['name'] }}</div>
                                <div class="text-sm text-gray-600 mt-1">ID: {{ $site['id'] }}</div>
                                <div class="text-sm text-gray-600 mt-1">Directory: {{ $site['directory'] }}</div>
                                <div class="text-sm text-gray-600 mt-1">Status: {{ $site['status'] }}</div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            @endforeach
        </ul>
    @else
        <p class="text-center text-gray-600 mt-4">No servers found.</p>
    @endif
</div>
