<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full antialiased bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, maximum-scale=5">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Manexo' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">

    <style>
        /* Typography */
        body { font-family: 'Inter', sans-serif; }
        .font-serif { font-family: 'Playfair Display', serif; }
        [x-cloak] { display: none !important; }
        html { overflow-x: hidden; }

        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #E2E8F0; border-radius: 999px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background-color: #CBD5E1; }

        /* Animations */
        @keyframes subtleFade { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: translateY(0); } }
        .animate-enter { animation: subtleFade 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }

        /* Organization accent color */
        ::selection { background: var(--accent-soft); color: var(--accent); }
    </style>
</head>
@php
    // currentOrganization is injected by EnsureOrganizationIsSelected middleware.
    $currentOrganization = $currentOrganization ?? request()->attributes->get('currentOrganization');
    $defaultAccent = env('MANEXO_DEFAULT_ACCENT', '#005F02');
    $accent = ($currentOrganization?->primary_color ?: $defaultAccent);
@endphp
<body
    class="flex h-screen w-full min-h-0 overflow-hidden bg-slate-50 text-slate-900 text-[14px] sm:text-[14px] lg:text-[15px] 2xl:text-[16px] min-[1920px]:text-[17px] min-[2560px]:text-[18px]"
    style="
        --accent: {{ $accent }};
        --accent-soft: color-mix(in srgb, var(--accent) 15%, white);
        --accent-soft-2: color-mix(in srgb, var(--accent) 25%, white);
        --accent-dark: color-mix(in srgb, var(--accent) 20%, black);
        --accent-ring: color-mix(in srgb, var(--accent) 20%, transparent);
    "
    x-data="{ sidebarOpen: true, mobileOpen: false }"
    x-init="sidebarOpen = (localStorage.getItem('manexo_sidebar') !== 'false')"
    x-effect="localStorage.setItem('manexo_sidebar', sidebarOpen)"
>

    <!-- Safety: ensure main app background is never tinted by branding -->
    <div class="pointer-events-none fixed inset-0 -z-10 bg-slate-50"></div>

    <!-- Mobile Overlay -->
    <div x-show="mobileOpen" x-transition.opacity class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm md:hidden" @click="mobileOpen = false"></div>

    <!-- SIDEBAR -->
    <x-manexo.sidebar />

    <!-- TOPBAR (fixed, always on top) -->
    <x-manexo.topbar />

    <!-- MAIN CONTENT: pt = hauteur du header (topbar) pour que le contenu reste sous le topbar -->
        <main
            class="flex-1 flex flex-col min-w-0 min-h-0 w-full max-w-full relative z-10 transition-[padding] duration-300 ease-[cubic-bezier(0.25,1,0.5,1)] pt-14 sm:pt-16 md:pl-[72px] xl:pl-[80px]"
        :class="sidebarOpen ? 'md:!pl-[240px] xl:!pl-[260px]' : ''"
    >
        <!-- PAGE BODY (scrollable) : padding horizontal pour ne pas coller au dashboard / bords -->
        <div class="page-content-safe flex-1 min-h-0 overflow-y-auto overflow-x-hidden custom-scrollbar pt-4 sm:pt-5 md:pt-6 lg:pt-8 xl:pt-10 2xl:pt-12 px-4 sm:px-5 md:px-6 lg:px-8 xl:px-10 2xl:px-12">
            <div class="mx-auto w-full min-w-0 max-w-7xl 2xl:max-w-[90rem] min-[1920px]:max-w-[110rem] min-[2560px]:max-w-[140rem] animate-enter space-y-4 sm:space-y-6">
                {{ $slot }}
            </div>
        </div>
    </main>

    {{-- Echo stub: @vite module scripts are deferred and execute AFTER regular
         scripts. Livewire's @livewireScripts is a regular <script> that runs
         first and needs window.Echo for echo-private: listeners. This no-op
         stub prevents the "Laravel Echo cannot be found" crash. The real Echo
         instance (from resources/js/echo.js via @vite) overwrites it once
         the module executes. --}}
    <script>
        if(!window.Echo){var _c={listen:function(){return _c},stopListening:function(){return _c},notification:function(){return _c},listenForWhisper:function(){return _c},subscribed:function(){return _c},error:function(){return _c}};window.Echo={private:function(){return _c},channel:function(){return _c},encryptedPrivate:function(){return _c},join:function(){return _c},leave:function(){},leaveChannel:function(){},leaveAllChannels:function(){},socketId:function(){return null},connector:{pusher:{connection:{state:"stub"}}}}}
    </script>
    @livewireScripts
</body>
</html>