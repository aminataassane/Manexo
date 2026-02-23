@props([
    'organization' => null,
    'title' => null,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full antialiased bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Manexo' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mona+Sans:ital,wght@0,200..900;1,200..900&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: "Mona Sans", sans-serif;
            font-optical-sizing: auto;
            font-style: normal;
            font-variation-settings: "wdth" 100;
        }
        [x-cloak] { display: none !important; }
        html { overflow-x: hidden; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #E2E8F0; border-radius: 999px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background-color: #CBD5E1; }
    </style>
</head>
@php
    /** @var \App\Models\Organization|null $organization */
    $org = $organization ?? null;
    $defaultAccent = env('MANEXO_DEFAULT_ACCENT', '#005F02');
    $accent = ($org?->primary_color ?: $defaultAccent);
@endphp
<body
    class="min-h-screen bg-slate-50 text-slate-900 text-[14px] sm:text-[14px] lg:text-[15px] 2xl:text-[16px] min-[1920px]:text-[17px]"
    style="
        --accent: {{ $accent }};
        --accent-soft: color-mix(in srgb, var(--accent) 15%, white);
        --accent-soft-2: color-mix(in srgb, var(--accent) 25%, white);
        --accent-dark: color-mix(in srgb, var(--accent) 20%, black);
        --accent-ring: color-mix(in srgb, var(--accent) 20%, transparent);
    "
>
    {{ $slot }}
</body>
</html>

