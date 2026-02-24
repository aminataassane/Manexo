<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>429 – {{ __('Trop de requêtes') }}</title>
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
    </style>
</head>
<body class="min-h-screen bg-slate-50 flex flex-col items-center justify-center px-4 py-8" style="--accent: {{ request()->attributes->get('currentOrganization')?->primary_color ?? env('MANEXO_DEFAULT_ACCENT', '#005F02') }};">
    <div class="w-full max-w-md text-center">
        {{-- Icône --}}
        <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-2xl border border-rose-200 bg-rose-50 text-rose-600 shadow-sm">
            <iconify-icon icon="solar:shield-warning-bold-duotone" width="44"></iconify-icon>
        </div>

        {{-- Code et titre --}}
        <h1 class="mt-6 text-4xl font-bold tracking-tight text-slate-900">
            <span class="text-slate-400">429</span>
            <span class="mx-2 text-slate-300">|</span>
            <span>{{ __('Trop de requêtes') }}</span>
        </h1>
        <p class="mt-3 text-sm leading-relaxed text-slate-600">
            {{ __('Vous avez effectué trop de requêtes. Veuillez patienter quelques instants avant de réessayer.') }}
        </p>

        {{-- Actions --}}
        @php
            $backUrl = url()->previous() !== url()->current() ? url()->previous() : (auth()->check() ? route('tickets.index') : route('home'));
        @endphp
        <div class="mt-8 flex flex-col items-center gap-3 sm:flex-row sm:justify-center">
            <a href="{{ $backUrl }}"
               class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition-colors hover:bg-slate-50">
                <iconify-icon icon="solar:arrow-left-linear" width="18"></iconify-icon>
                {{ __('Retour') }}
            </a>
            <a href="javascript:location.reload()"
               class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-bold text-white shadow-sm transition-opacity hover:opacity-90"
               style="background: var(--accent);">
                <iconify-icon icon="solar:refresh-linear" width="18"></iconify-icon>
                {{ __('Réessayer') }}
            </a>
        </div>
    </div>
</body>
</html>
