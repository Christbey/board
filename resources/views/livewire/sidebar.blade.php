<aside :class="{ 'closed': !isSidebarOpen }"
       class="fixed inset-y-0 left-0 bg-white shadow-md z-30 lg:relative lg:flex-shrink-0 overflow-y-auto closed"
       id="sidebar">
    <div class="p-6 h-full flex flex-col">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Menu</h2>
            <button @click="isSidebarOpen = false" class="text-gray-500 cursor-pointer lg:hidden">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12"></path>
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

                <!-- NFL Dropdown -->
                <x-nav-dropdown label="NFL" id="dropdown-nfl">
                    <x-nav-link href="{{ route('nfl.teams') }}" :active="request()->routeIs('nfl.teams')">
                        {{ __('Teams') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('nfl.event') }}" :active="request()->routeIs('nfl.event')">
                        {{ __('Events') }}
                    </x-nav-link>
                </x-nav-dropdown>
                <!-- MLB Dropdown -->
                <x-nav-dropdown label="MLB" id="dropdown-mlb">
                    <x-nav-link href="{{ route('mlb.teams') }}" :active="request()->routeIs('mlb.teams')">
                        {{ __('Teams') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('mlb.event') }}" :active="request()->routeIs('mlb.event')">
                        {{ __('Events') }}
                    </x-nav-link>
                </x-nav-dropdown>
                <!-- NBA Dropdown -->
                <x-nav-dropdown label="NBA" id="dropdown-nba">
                    <x-nav-link href="{{ route('nba.teams') }}" :active="request()->routeIs('nba.teams')">
                        {{ __('Teams') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('nba.event') }}" :active="request()->routeIs('nba.event')">
                        {{ __('Events') }}
                    </x-nav-link>
                </x-nav-dropdown>
                <!-- NCAAF Dropdown -->
                <x-nav-dropdown label="NCAAF" id="dropdown-ncaaf">
                    <x-nav-link href="{{ route('ncaa.teams') }}" :active="request()->routeIs('ncaa.teams')">
                        {{ __('Teams') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('ncaa.event') }}" :active="request()->routeIs('ncaa.event')">
                        {{ __('Events') }}
                    </x-nav-link>
                </x-nav-dropdown>
                <!-- Management Dropdown -->
                <x-nav-dropdown label="Management" id="dropdown-management">
                    <!-- Team Management -->
                    <div class="block px-4 py-2 text-xs text-gray-400">
                        {{ __('Manage Team') }}
                    </div>
                    <x-nav-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}">
                        {{ __('Team Settings') }}
                    </x-nav-link>
                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                        <x-nav-link href="{{ route('teams.create') }}">
                            {{ __('Create New Team') }}
                        </x-nav-link>
                    @endcan
                    @if (Auth::user()->allTeams()->count() > 1)
                        <div class="border-t border-gray-200"></div>
                        <div class="block px-4 py-2 text-xs text-gray-400">
                            {{ __('Switch Teams') }}
                        </div>
                        @foreach (Auth::user()->allTeams() as $team)
                            <x-switchable-team :team="$team"/>
                        @endforeach
                    @endif
                    <!-- Account Management -->
                    <div class="block px-4 py-2 text-xs text-gray-400">
                        {{ __('Manage Account') }}
                    </div>
                    <x-nav-link href="{{ route('profile.show') }}">
                        {{ __('Profile') }}
                    </x-nav-link>
                    @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                        @if (auth()->user()->email === 'josh@picksports.app')
                            <x-nav-link href="{{ route('api-tokens.index') }}">
                                {{ __('API Tokens') }}
                            </x-nav-link>
                        @endif
                    @endif
                </x-nav-dropdown>
            </ul>
            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}" x-data>
                @csrf
                <x-nav-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                    {{ __('Log Out') }}
                </x-nav-link>
            </form>
        </nav>
    </div>
</aside>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const closeButton = document.getElementById('closeSidebarBtn');
        const sidebar = document.getElementById('sidebar');

        if (closeButton && sidebar) {
            closeButton.addEventListener('click', function () {
                sidebar.classList.toggle('closed');
                document.getElementById('mainContent').classList.toggle('closed');
            });
        }
    });

    function toggleDropdown(id) {
        var dropdown = document.getElementById(id);
        dropdown.classList.toggle('hidden');
    }
</script>