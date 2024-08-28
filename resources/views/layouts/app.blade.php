<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Pack') }}</title>
    <link rel="icon" href="{{ asset('/images/logowofl.svg') }}" type="image/svg+xml">

    <!-- Preconnect to font and other resources -->
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link rel="preload" as="style" href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap">
    <link rel="stylesheet" href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap"/>

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
          integrity="sha384-5e2ESR8Ycmos6g3gAKr1Jvwye8sW4U1u/cAKulfVJnkakCcMqhOudbtPnvJ+nbv7" crossorigin="anonymous">

    <!-- Scripts -->
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-50" x-data="{ isSidebarOpen: false }">
<div class="flex">
    <!-- Sidebar -->
    @livewire('sidebar')

    <!-- Main Content -->
    <div id="mainContent" :class="{'closed': !isSidebarOpen}" class="flex-1 flex flex-col overflow-y-auto">
        <header class="border-b p-4 flex items-center justify-between">
            <button @click="isSidebarOpen = !isSidebarOpen" class="text-gray-500 cursor-pointer">
                <svg x-show="!isSidebarOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16M4 12h16m-7 6h7"></path>
                </svg>
                <svg x-show="isSidebarOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </header>

        {{ $slot }}
        <footer class="py-3 text-center text-sm text-gray-700">
            Bey v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
        </footer>
    </div>

</div>

@stack('modals')
@livewireScripts
<x-flash-message/>
</body>
</html>
