@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 text-gray-900 focus:outline-none transition duration-150 ease-in-out border-[color:var(--accent)] focus:border-[color:var(--accent)]'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-[color:var(--accent)] hover:border-[color:var(--accent)] focus:outline-none focus:text-[color:var(--accent)] focus:border-[color:var(--accent)] transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
