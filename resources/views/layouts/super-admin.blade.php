<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full antialiased bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, maximum-scale=5">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? __('super_admin.title') }} — Platform Admin</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mona+Sans:ital,wght@0,200..900;1,200..900&display=swap" rel="stylesheet">

    <style>
        body, .font-serif {
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
        @keyframes subtleFade { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: translateY(0); } }
        .animate-enter { animation: subtleFade 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        ::selection { background: #e0e7ff; color: #4f46e5; }
    </style>
</head>
<body
    class="flex h-screen w-full min-h-0 overflow-hidden bg-slate-50 text-slate-900 text-[14px] sm:text-[14px] lg:text-[15px]"
    x-data="{ sidebarOpen: true, mobileOpen: false }"
    x-init="sidebarOpen = (localStorage.getItem('sa_sidebar') !== 'false')"
    x-effect="localStorage.setItem('sa_sidebar', sidebarOpen)"
>
    <div class="pointer-events-none fixed inset-0 -z-10 bg-slate-50"></div>

    <!-- Mobile Overlay -->
    <div x-show="mobileOpen" x-transition.opacity class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm md:hidden" @click="mobileOpen = false"></div>

    <!-- SIDEBAR -->
    @php
        $isSaDashboard = request()->routeIs('platform-admin.dashboard');
        $isSaOrgs = request()->routeIs('platform-admin.organizations') || request()->routeIs('platform-admin.organizations.show');
        $isSaAudit = request()->routeIs('platform-admin.audit-log');
    @endphp
    <aside
        class="fixed left-0 top-0 z-40 flex h-screen shrink-0 flex-col border-r border-white/5 text-white/60 transition-all duration-300 ease-[cubic-bezier(0.25,1,0.5,1)] max-w-[85vw] md:max-w-none backdrop-blur-xl"
        style="background: linear-gradient(180deg, #312e81 0%, #1e1b4b 100%);"
        :class="{
            'w-[240px] xl:w-[260px]': sidebarOpen,
            'w-[72px] xl:w-[80px]': !sidebarOpen,
            '-translate-x-full md:translate-x-0': !mobileOpen,
            'translate-x-0': mobileOpen
        }"
    >
        <!-- Logo -->
        <div class="flex h-16 shrink-0 items-center px-4 xl:px-5" :class="sidebarOpen ? 'justify-start' : 'justify-center'">
            <a href="{{ route('platform-admin.dashboard') }}" class="flex items-center gap-3 group transition-all duration-300">
                <span class="relative h-9 w-9 shrink-0 rounded-xl overflow-hidden ring-1 ring-white/10 shadow-lg flex items-center justify-center bg-indigo-500">
                    <iconify-icon icon="solar:shield-star-bold-duotone" width="20" class="text-white"></iconify-icon>
                </span>
                <div class="flex flex-col min-w-0" x-show="sidebarOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0">
                    <span class="text-sm font-bold text-white tracking-wide">PLATFORM ADMIN</span>
                    <span class="text-[10px] font-medium text-white/40 uppercase tracking-widest">Manexo</span>
                </div>
            </a>
        </div>

        <!-- Navigation -->
        <div class="custom-scrollbar flex flex-1 flex-col gap-1 overflow-y-auto px-3 py-4">
            <div class="px-3 mb-2 mt-2 text-[10px] font-bold uppercase tracking-widest text-white/30 transition-opacity duration-300" x-show="sidebarOpen">
                {{ __('super_admin.nav.menu') }}
            </div>

            <!-- Dashboard -->
            <a
                href="{{ route('platform-admin.dashboard') }}"
                class="group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200 {{ $isSaDashboard ? 'text-white bg-white/10 shadow-sm ring-1 ring-white/5' : 'text-white/60 hover:bg-white/5 hover:text-white' }}"
                :class="sidebarOpen ? '' : 'justify-center'"
            >
                @if($isSaDashboard)
                    <div class="absolute left-0 h-6 w-1 rounded-r-full bg-indigo-400 shadow-[0_0_10px_rgb(129,140,248)]" x-show="sidebarOpen"></div>
                @endif
                <iconify-icon icon="solar:widget-5-bold-duotone" width="20" class="{{ $isSaDashboard ? 'text-indigo-300' : 'text-white/50 group-hover:text-white/80' }} transition-colors"></iconify-icon>
                <span x-show="sidebarOpen" class="truncate">{{ __('super_admin.nav.dashboard') }}</span>
                <div x-show="!sidebarOpen" class="absolute left-full ml-2 hidden rounded-md bg-slate-900 px-2 py-1 text-xs text-white opacity-0 group-hover:block group-hover:opacity-100 z-50 whitespace-nowrap shadow-xl">
                    {{ __('super_admin.nav.dashboard') }}
                </div>
            </a>

            <!-- Organizations -->
            <a
                href="{{ route('platform-admin.organizations') }}"
                class="group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200 {{ $isSaOrgs ? 'text-white bg-white/10 shadow-sm ring-1 ring-white/5' : 'text-white/60 hover:bg-white/5 hover:text-white' }}"
                :class="sidebarOpen ? '' : 'justify-center'"
            >
                @if($isSaOrgs)
                    <div class="absolute left-0 h-6 w-1 rounded-r-full bg-indigo-400 shadow-[0_0_10px_rgb(129,140,248)]" x-show="sidebarOpen"></div>
                @endif
                <iconify-icon icon="solar:buildings-bold-duotone" width="20" class="{{ $isSaOrgs ? 'text-indigo-300' : 'text-white/50 group-hover:text-white/80' }} transition-colors"></iconify-icon>
                <span x-show="sidebarOpen" class="truncate">{{ __('super_admin.nav.organizations') }}</span>
                <div x-show="!sidebarOpen" class="absolute left-full ml-2 hidden rounded-md bg-slate-900 px-2 py-1 text-xs text-white opacity-0 group-hover:block group-hover:opacity-100 z-50 whitespace-nowrap shadow-xl">
                    {{ __('super_admin.nav.organizations') }}
                </div>
            </a>

            <!-- Audit Log -->
            <a
                href="{{ route('platform-admin.audit-log') }}"
                class="group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200 {{ $isSaAudit ? 'text-white bg-white/10 shadow-sm ring-1 ring-white/5' : 'text-white/60 hover:bg-white/5 hover:text-white' }}"
                :class="sidebarOpen ? '' : 'justify-center'"
            >
                @if($isSaAudit)
                    <div class="absolute left-0 h-6 w-1 rounded-r-full bg-indigo-400 shadow-[0_0_10px_rgb(129,140,248)]" x-show="sidebarOpen"></div>
                @endif
                <iconify-icon icon="solar:document-text-bold-duotone" width="20" class="{{ $isSaAudit ? 'text-indigo-300' : 'text-white/50 group-hover:text-white/80' }} transition-colors"></iconify-icon>
                <span x-show="sidebarOpen" class="truncate">{{ __('super_admin.nav.audit_log') }}</span>
                <div x-show="!sidebarOpen" class="absolute left-full ml-2 hidden rounded-md bg-slate-900 px-2 py-1 text-xs text-white opacity-0 group-hover:block group-hover:opacity-100 z-50 whitespace-nowrap shadow-xl">
                    {{ __('super_admin.nav.audit_log') }}
                </div>
            </a>

        </div>

        <!-- Collapse Button -->
        <button
            type="button"
            class="absolute -right-3 top-20 hidden md:flex h-6 w-6 items-center justify-center rounded-full bg-white text-slate-400 shadow-md ring-1 ring-slate-100 hover:text-indigo-600 transition-colors z-50"
            @click="sidebarOpen = !sidebarOpen"
        >
            <iconify-icon :icon="sidebarOpen ? 'solar:alt-arrow-left-linear' : 'solar:alt-arrow-right-linear'" width="14"></iconify-icon>
        </button>
    </aside>

    <!-- TOPBAR -->
    <header
        class="fixed top-0 right-0 z-30 flex h-14 sm:h-16 items-center justify-between border-b border-slate-200/60 bg-white/80 backdrop-blur-lg px-4 sm:px-6 transition-[left] duration-300 ease-[cubic-bezier(0.25,1,0.5,1)] md:left-[72px] xl:left-[80px] left-0"
        :class="sidebarOpen ? 'md:!left-[240px] xl:!left-[260px]' : ''"
    >
        <div class="flex items-center gap-3">
            <!-- Mobile hamburger -->
            <button type="button" class="md:hidden flex items-center justify-center h-9 w-9 rounded-lg hover:bg-slate-100 transition-colors" @click="mobileOpen = !mobileOpen">
                <iconify-icon icon="solar:hamburger-menu-linear" width="20" class="text-slate-500"></iconify-icon>
            </button>
            <h1 class="text-base font-semibold text-slate-800">{{ $title ?? __('super_admin.title') }}</h1>
        </div>

        <div class="flex items-center gap-3">
            <!-- User dropdown -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex items-center gap-2 rounded-lg px-2.5 py-1.5 hover:bg-slate-100 transition-colors">
                    <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                        <span class="text-xs font-bold text-indigo-600">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                    </div>
                    <span class="hidden sm:block text-sm font-medium text-slate-700">{{ auth()->user()->name }}</span>
                    <iconify-icon icon="solar:alt-arrow-down-linear" width="12" class="text-slate-400"></iconify-icon>
                </button>

                <div x-show="open" @click.away="open = false" x-transition
                     class="absolute right-0 mt-2 w-48 rounded-xl bg-white shadow-xl ring-1 ring-slate-200/60 py-1 z-50">
                    <a href="{{ route('profile') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                        <iconify-icon icon="solar:user-bold-duotone" width="16" class="text-slate-400"></iconify-icon>
                        {{ __('super_admin.nav.profile') }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                            <iconify-icon icon="solar:logout-2-bold-duotone" width="16" class="text-slate-400"></iconify-icon>
                            {{ __('super_admin.nav.logout') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main
        class="flex-1 flex flex-col min-w-0 min-h-0 w-full max-w-full relative z-10 transition-[padding] duration-300 ease-[cubic-bezier(0.25,1,0.5,1)] pt-14 sm:pt-16 md:pl-[72px] xl:pl-[80px]"
        :class="sidebarOpen ? 'md:!pl-[240px] xl:!pl-[260px]' : ''"
    >
        <div class="page-content-safe flex-1 min-h-0 overflow-y-auto overflow-x-hidden custom-scrollbar pt-4 sm:pt-5 md:pt-6 lg:pt-8 px-4 sm:px-5 md:px-6 lg:px-8">
            <div class="mx-auto w-full min-w-0 max-w-7xl animate-enter space-y-4 sm:space-y-6">
                {{-- Session flash messages --}}
                @if (session('success'))
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        {{ session('error') }}
                    </div>
                @endif

                {{ $slot }}
            </div>
        </div>
    </main>

    {{-- Echo stub --}}
    <script>
        if(!window.Echo){var _c={listen:function(){return _c},stopListening:function(){return _c},notification:function(){return _c},listenForWhisper:function(){return _c},subscribed:function(){return _c},error:function(){return _c}};window.Echo={private:function(){return _c},channel:function(){return _c},encryptedPrivate:function(){return _c},join:function(){return _c},leave:function(){},leaveChannel:function(){},leaveAllChannels:function(){},socketId:function(){return null},connector:{pusher:{connection:{state:"stub"}}}}}
    </script>
    @livewireScripts
    {{-- Loading bar --}}
    <div id="livewire-loading-bar" class="fixed top-0 left-0 right-0 h-0.5 z-[100] opacity-0 transition-opacity duration-150 pointer-events-none" style="background: #4f46e5; transform: scaleX(0); transform-origin: left;"></div>
    <script>
        document.addEventListener('livewire:init', function() {
            var bar = document.getElementById('livewire-loading-bar');
            if (!bar) return;
            Livewire.hook('request', function({ uri, options }) {
                bar.style.opacity = '1';
                bar.style.transform = 'scaleX(0.3)';
            });
            Livewire.hook('commit', function({ component, commit, respond, succeed, fail }) {
                succeed(function() { bar.style.transform = 'scaleX(1)'; bar.style.opacity = '0'; });
                fail(function() { bar.style.opacity = '0'; bar.style.transform = 'scaleX(0)'; });
            });
        });
    </script>
</body>
</html>
