@props(['label', 'id'])

<li class="relative">
    <button class="flex items-center justify-between w-full py-2 px-4 text-gray-700 hover:text-gray-900 focus:outline-none" onclick="toggleDropdown('{{ $id }}')">
        <span>{{ $label }}</span>
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>
    <ul id="{{ $id }}" class="hidden mt-2 space-y-2">
        {{ $slot }}
    </ul>
</li>