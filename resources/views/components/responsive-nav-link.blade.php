@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 text-start text-base font-medium focus:outline-none transition duration-150 ease-in-out border-[color:var(--accent)] text-[color:var(--accent)] bg-[color:color-mix(in srgb, var(--accent) 10%, white)] focus:border-[color:var(--accent)]'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 hover:text-[color:var(--accent)] hover:bg-[color:color-mix(in srgb, var(--accent) 6%, white)] hover:border-[color:var(--accent)] focus:outline-none focus:text-[color:var(--accent)] focus:bg-[color:color-mix(in srgb, var(--accent) 8%, white)] focus:border-[color:var(--accent)] transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
