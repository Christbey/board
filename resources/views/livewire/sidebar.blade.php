<aside id="sidebar" class="fixed inset-y-0 left-0 bg-white shadow-md z-30 lg:relative lg:flex-shrink-0 overflow-y-auto closed">
    <div class="p-6 h-full flex flex-col">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Menu</h2>
            <button id="closeSidebarBtn" class="text-gray-500 cursor-pointer lg:hidden">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <nav class="flex-1">
            <ul>
                <li>
                    <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </li>
                <li>
                    <x-nav-link href="{{ route('tasks.index') }}" :active="request()->routeIs('tasks.index')">
                        {{ __('Tasks') }}
                    </x-nav-link>
                </li>
                <li>
                    <x-nav-link href="{{ route('odds.index') }}" :active="request()->routeIs('odds.index')">
                        {{ __('Odds') }}
                    </x-nav-link>
                </li>
                <!-- Dropdown example -->
                <li class="relative">
                    <button class="flex items-center justify-between w-full py-2 text-gray-700 hover:text-gray-900 focus:outline-none" onclick="toggleDropdown('dropdown-1')">
                        <span>{{ __('NFL') }}</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
{{--                    <ul id="dropdown-1" class="hidden mt-2 space-y-2">--}}
{{--                        <li>--}}
{{--                            <x-nav-link href="{{ route('nfl.index') }}" :active="request()->routeIs('nfl.index')">--}}
{{--                                {{ __('Team List') }}--}}
{{--                            </x-nav-link>--}}
{{--                        </li>--}}
{{--                        <li>--}}
{{--                            <x-nav-link href="{{ route('nfl.odds') }}" :active="request()->routeIs('nfl.odds')">--}}
{{--                                {{ __('Odds') }}--}}
{{--                            </x-nav-link>--}}
{{--                        </li>--}}
{{--                    </ul>--}}
                </li>
                <!-- Add more dropdowns as needed -->
            </ul>
        </nav>
    </div>
</aside>

<script>
    function toggleDropdown(id) {
        var dropdown = document.getElementById(id);
        dropdown.classList.toggle('hidden');
    }
</script>
