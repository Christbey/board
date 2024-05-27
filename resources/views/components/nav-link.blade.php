@props(['active'])

@php
    $classes = ($active ?? false)
                ? 'block py-2 px-4 text-gray-700 bg-gray-200 rounded transition duration-200 ease-in-out'
                : 'block py-2 px-4 text-gray-700 hover:bg-gray-200 rounded transition duration-200 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
