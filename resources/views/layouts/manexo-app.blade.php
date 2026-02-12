<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Manexo' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; letter-spacing: -0.01em; }
        .font-serif { font-family: 'Playfair Display', serif; }
        [x-cloak] { display: none !important; }

        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #E5E7EB; border-radius: 999px; }

        /* Animations */
        @keyframes subtleFade { from { opacity: 0; transform: translateY(2px); } to { opacity: 1; transform: translateY(0); } }
        .animate-enter { animation: subtleFade 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }

        /* Utilities */
        iconify-icon { display: inline-flex; align-items: center; justify-content: center; vertical-align: text-bottom; }

        /* Organization accent color (CSS variable: --accent) */
        ::selection { background: var(--accent-soft); color: var(--accent); }

        .accent-text { color: var(--accent); }
        .accent-bg { background-color: var(--accent); }
        .accent-border { border-color: var(--accent); }

        .accent-hover:hover { color: var(--accent); }
        .accent-bg-hover:hover { background-color: color-mix(in srgb, var(--accent) 8%, white); }

        /* CSS Bar Chart */
        .bar { transition: height 0.5s ease; }
        .bar:hover { opacity: 0.9; }
    </style>
</head>
@php
    $currentOrganization = request()->attributes->get('currentOrganization') ?? \App\Models\Organization::find(session('current_organization_id'));
    $defaultAccent = env('MANEXO_DEFAULT_ACCENT', '#005F02');
    $accent = ($currentOrganization?->primary_color ?: $defaultAccent);
@endphp
<body
    class="flex h-screen w-full overflow-hidden bg-[#F9FAFB] text-[#111827]"
    style="
        --accent: {{ $accent }};
        --accent-soft: color-mix(in srgb, var(--accent) 18%, white);
        --accent-soft-2: color-mix(in srgb, var(--accent) 28%, white);
        --accent-ring: color-mix(in srgb, var(--accent) 20%, transparent);
    "
    x-data="{ sidebarOpen: true, mobileOpen: false }"
>

    <!-- Mobile Overlay -->
    <div x-show="mobileOpen" x-transition.opacity class="fixed inset-0 z-40 bg-slate-900/50 md:hidden" @click="mobileOpen = false"></div>

    <!-- Sidebar -->
    <x-manexo.sidebar />

    <!-- MAIN CONTENT WRAPPER -->
    <main
        class="flex-1 flex flex-col min-w-0 bg-[#F9FAFB] relative transition-[padding] duration-300 ease-in-out"
        :class="sidebarOpen ? 'md:pl-[220px]' : 'md:pl-[72px]'"
    >
        <!-- TOPBAR -->
        <x-manexo.topbar />

        <!-- PAGE BODY (scroll) -->
        <div class="flex-1 overflow-y-auto custom-scrollbar p-4 sm:p-6">
            <div class="max-w-7xl mx-auto space-y-6 animate-enter">
                {{ $slot }}
            </div>
        </div>
    </main>

    @livewireScripts
</body>
</html>
