<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full antialiased bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, maximum-scale=5">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __($title ?? 'super_admin.title') }} — Platform Admin</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Logo(1).png') }}">

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
        ::selection { background: #F2E3BB; color: #005F02; }
        /* En sidebar repliée, on neutralise les mini-tooltips (évite texte qui déborde) */
        aside .group > .absolute.left-full { display: none !important; }

        /* Boutons platform admin — contraste élevé, lisibilité */
        .sa-btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.625rem 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: #fff !important;
            background: #005F02 !important;
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.12);
            transition: background 0.2s, box-shadow 0.2s;
        }
        .sa-btn-primary:hover:not(:disabled) {
            background: #004d02 !important;
            box-shadow: 0 2px 6px rgba(0, 95, 2, 0.35);
        }
        .sa-btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }

        .sa-btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.625rem 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: #334155 !important;
            background: #fff !important;
            border: 1px solid #cbd5e1;
            border-radius: 0.75rem;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            transition: background 0.2s, border-color 0.2s;
        }
        .sa-btn-secondary:hover {
            background: #f8fafc !important;
            border-color: #94a3b8;
        }

        .sa-btn-ghost {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.25rem;
            padding: 0.375rem 0.625rem;
            font-size: 0.75rem;
            font-weight: 500;
            color: #475569 !important;
            background: transparent;
            border: none;
            border-radius: 0.5rem;
            transition: background 0.15s;
        }
        .sa-btn-ghost:hover { background: #f1f5f9 !important; }

        /* Tableaux platform admin — contraste renforcé, lisibilité */
        .page-content-safe table { border-collapse: collapse; }
        .page-content-safe thead { background: #f1f5f9 !important; }
        .page-content-safe thead th {
            color: #334155 !important;
            font-weight: 600;
            border-bottom: 1px solid #e2e8f0 !important;
        }
        .page-content-safe tbody td { color: #1e293b !important; }
        .page-content-safe tbody tr:hover { background: #f8fafc !important; }
        .page-content-safe tbody tr { border-bottom: 1px solid #e2e8f0; }
        .page-content-safe table td .rounded-full.bg-indigo-50,
        .page-content-safe table td .rounded-full.border-indigo-200 {
            background: #005F02 !important;
            color: #fff !important;
            border-color: #004d02 !important;
        }
    </style>
</head>
<body
    class="manexo-fluid-root flex h-screen w-full min-h-0 overflow-hidden bg-slate-50 text-slate-900"
    data-echo-enabled="1"
    x-data="{ sidebarOpen: true, mobileOpen: false }"
    x-init="sidebarOpen = (localStorage.getItem('sa_sidebar') !== 'false'); document.body.style.setProperty('--manexo-shell-offset', sidebarOpen ? 'var(--manexo-sidebar-expanded)' : 'var(--manexo-sidebar-collapsed)')"
    x-effect="localStorage.setItem('sa_sidebar', sidebarOpen); document.body.style.setProperty('--manexo-shell-offset', sidebarOpen ? 'var(--manexo-sidebar-expanded)' : 'var(--manexo-sidebar-collapsed)')"
>
    <div class="pointer-events-none fixed inset-0 -z-10 bg-slate-50"></div>

    <!-- Mobile Overlay -->
    <div x-show="mobileOpen" x-transition.opacity class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm md:hidden" @click="mobileOpen = false"></div>

    <!-- SIDEBAR -->
    @php
        $isSaDashboard = request()->routeIs('platform-admin.dashboard');
        $isSaOrgs = request()->routeIs('platform-admin.organizations') || request()->routeIs('platform-admin.organizations.show');
        $isSaAudit = request()->routeIs('platform-admin.audit-log') || request()->routeIs('platform-admin.audit-log.export');
        $isSaUsers = request()->routeIs('platform-admin.users');
        $isSaSecurity = request()->routeIs('platform-admin.security');
        $isSaFiles = request()->routeIs('platform-admin.files');
        $isSaNotifications = request()->routeIs('platform-admin.notifications');
        $isSaMonitoring = request()->routeIs('platform-admin.monitoring');
        $isSaBackups = request()->routeIs('platform-admin.backups');
        $isSaSupport = request()->routeIs('platform-admin.support-sessions');
    @endphp
    <aside
        class="manexo-shell-transition fixed left-0 top-0 z-40 flex h-screen shrink-0 flex-col border-r border-white/5 text-white/60 transition-[width] duration-300 ease-[cubic-bezier(0.25,1,0.5,1)] max-w-[85vw] md:max-w-none backdrop-blur-xl"
        style="background: linear-gradient(180deg, #002e01 0%, #001a01 100%);"
        :class="{
            'w-[min(85vw,var(--manexo-sidebar-expanded))]': sidebarOpen,
            'w-[var(--manexo-sidebar-collapsed)]': !sidebarOpen,
            '-translate-x-full md:translate-x-0': !mobileOpen,
            'translate-x-0': mobileOpen
        }"
    >
        <!-- Logo + nom de la plateforme -->
        @php
            $platformName = config('app.platform_creator.name', 'Manexo');
            $platformLogoPath = 'assets/Logo(1).png';
            $platformLogoUrl = \Illuminate\Support\Facades\File::exists(public_path($platformLogoPath)) ? asset($platformLogoPath) : null;
        @endphp
        <div class="flex h-16 shrink-0 items-center px-4 xl:px-5" :class="sidebarOpen ? 'justify-start' : 'justify-center'">
            <a href="{{ route('platform-admin.dashboard') }}" class="flex items-center gap-3 group transition-all duration-300">
                <span class="relative h-9 w-9 shrink-0 rounded-lg overflow-hidden ring-1 ring-white/10 shadow-lg flex items-center justify-center bg-white p-1">
                    @if($platformLogoUrl)
                        <img src="{{ $platformLogoUrl }}" alt="{{ $platformName }}" class="h-full w-full object-contain" />
                    @else
                        <iconify-icon icon="solar:shield-star-bold-duotone" width="20" class="text-[#005F02]"></iconify-icon>
                    @endif
                </span>
                <div class="flex flex-col min-w-0" x-show="sidebarOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0">
                    <span class="text-sm font-bold text-white tracking-wide">PLATFORM ADMIN</span>
                    <span class="text-[10px] font-medium text-white/40 uppercase tracking-widest">{{ $platformName }}</span>
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
                    <div class="absolute left-0 h-6 w-1 rounded-r-full bg-[#F2E3BB] shadow-[0_0_10px_rgba(242,227,187,0.6)]" x-show="sidebarOpen"></div>
                @endif
                <iconify-icon icon="solar:widget-5-bold-duotone" width="20" class="{{ $isSaDashboard ? 'text-[#F2E3BB]' : 'text-white/50 group-hover:text-white/80' }} transition-colors"></iconify-icon>
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
                    <div class="absolute left-0 h-6 w-1 rounded-r-full bg-[#F2E3BB] shadow-[0_0_10px_rgba(242,227,187,0.6)]" x-show="sidebarOpen"></div>
                @endif
                <iconify-icon icon="solar:buildings-bold-duotone" width="20" class="{{ $isSaOrgs ? 'text-[#F2E3BB]' : 'text-white/50 group-hover:text-white/80' }} transition-colors"></iconify-icon>
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
                    <div class="absolute left-0 h-6 w-1 rounded-r-full bg-[#F2E3BB] shadow-[0_0_10px_rgba(242,227,187,0.6)]" x-show="sidebarOpen"></div>
                @endif
                <iconify-icon icon="solar:document-text-bold-duotone" width="20" class="{{ $isSaAudit ? 'text-[#F2E3BB]' : 'text-white/50 group-hover:text-white/80' }} transition-colors"></iconify-icon>
                <span x-show="sidebarOpen" class="truncate">{{ __('super_admin.nav.audit_log') }}</span>
                <div x-show="!sidebarOpen" class="absolute left-full ml-2 hidden rounded-md bg-slate-900 px-2 py-1 text-xs text-white opacity-0 group-hover:block group-hover:opacity-100 z-50 whitespace-nowrap shadow-xl">
                    {{ __('super_admin.nav.audit_log') }}
                </div>
            </a>

            <!-- Section: Gestion -->
            <div class="px-3 mb-2 mt-4 text-[10px] font-bold uppercase tracking-widest text-white/30 transition-opacity duration-300" x-show="sidebarOpen">
                {{ __('super_admin.nav.section_management') }}
            </div>

            <!-- Users -->
            <a
                href="{{ route('platform-admin.users') }}"
                class="group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200 {{ $isSaUsers ? 'text-white bg-white/10 shadow-sm ring-1 ring-white/5' : 'text-white/60 hover:bg-white/5 hover:text-white' }}"
                :class="sidebarOpen ? '' : 'justify-center'"
            >
                @if($isSaUsers)
                    <div class="absolute left-0 h-6 w-1 rounded-r-full bg-[#F2E3BB] shadow-[0_0_10px_rgba(242,227,187,0.6)]" x-show="sidebarOpen"></div>
                @endif
                <iconify-icon icon="solar:users-group-rounded-bold-duotone" width="20" class="{{ $isSaUsers ? 'text-[#F2E3BB]' : 'text-white/50 group-hover:text-white/80' }} transition-colors"></iconify-icon>
                <span x-show="sidebarOpen" class="truncate">{{ __('super_admin.nav.users') }}</span>
                <div x-show="!sidebarOpen" class="absolute left-full ml-2 hidden rounded-md bg-slate-900 px-2 py-1 text-xs text-white opacity-0 group-hover:block group-hover:opacity-100 z-50 whitespace-nowrap shadow-xl">
                    {{ __('super_admin.nav.users') }}
                </div>
            </a>

            <!-- Security (super_admin only) -->
            @if(auth()->user()->canPlatformAdminister())
            <a
                href="{{ route('platform-admin.security') }}"
                class="group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200 {{ $isSaSecurity ? 'text-white bg-white/10 shadow-sm ring-1 ring-white/5' : 'text-white/60 hover:bg-white/5 hover:text-white' }}"
                :class="sidebarOpen ? '' : 'justify-center'"
            >
                @if($isSaSecurity)
                    <div class="absolute left-0 h-6 w-1 rounded-r-full bg-[#F2E3BB] shadow-[0_0_10px_rgba(242,227,187,0.6)]" x-show="sidebarOpen"></div>
                @endif
                <iconify-icon icon="solar:shield-keyhole-bold-duotone" width="20" class="{{ $isSaSecurity ? 'text-[#F2E3BB]' : 'text-white/50 group-hover:text-white/80' }} transition-colors"></iconify-icon>
                <span x-show="sidebarOpen" class="truncate">{{ __('super_admin.nav.security') }}</span>
                <div x-show="!sidebarOpen" class="absolute left-full ml-2 hidden rounded-md bg-slate-900 px-2 py-1 text-xs text-white opacity-0 group-hover:block group-hover:opacity-100 z-50 whitespace-nowrap shadow-xl">
                    {{ __('super_admin.nav.security') }}
                </div>
            </a>
            @endif

            <!-- Section: Système -->
            <div class="px-3 mb-2 mt-4 text-[10px] font-bold uppercase tracking-widest text-white/30 transition-opacity duration-300" x-show="sidebarOpen">
                {{ __('super_admin.nav.section_system') }}
            </div>

            <!-- Files -->
            <a
                href="{{ route('platform-admin.files') }}"
                class="group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200 {{ $isSaFiles ? 'text-white bg-white/10 shadow-sm ring-1 ring-white/5' : 'text-white/60 hover:bg-white/5 hover:text-white' }}"
                :class="sidebarOpen ? '' : 'justify-center'"
            >
                @if($isSaFiles)
                    <div class="absolute left-0 h-6 w-1 rounded-r-full bg-[#F2E3BB] shadow-[0_0_10px_rgba(242,227,187,0.6)]" x-show="sidebarOpen"></div>
                @endif
                <iconify-icon icon="solar:folder-with-files-bold-duotone" width="20" class="{{ $isSaFiles ? 'text-[#F2E3BB]' : 'text-white/50 group-hover:text-white/80' }} transition-colors"></iconify-icon>
                <span x-show="sidebarOpen" class="truncate">{{ __('super_admin.nav.files') }}</span>
                <div x-show="!sidebarOpen" class="absolute left-full ml-2 hidden rounded-md bg-slate-900 px-2 py-1 text-xs text-white opacity-0 group-hover:block group-hover:opacity-100 z-50 whitespace-nowrap shadow-xl">
                    {{ __('super_admin.nav.files') }}
                </div>
            </a>

            <!-- Notifications -->
            <a
                href="{{ route('platform-admin.notifications') }}"
                class="group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200 {{ $isSaNotifications ? 'text-white bg-white/10 shadow-sm ring-1 ring-white/5' : 'text-white/60 hover:bg-white/5 hover:text-white' }}"
                :class="sidebarOpen ? '' : 'justify-center'"
            >
                @if($isSaNotifications)
                    <div class="absolute left-0 h-6 w-1 rounded-r-full bg-[#F2E3BB] shadow-[0_0_10px_rgba(242,227,187,0.6)]" x-show="sidebarOpen"></div>
                @endif
                <iconify-icon icon="solar:bell-bold-duotone" width="20" class="{{ $isSaNotifications ? 'text-[#F2E3BB]' : 'text-white/50 group-hover:text-white/80' }} transition-colors"></iconify-icon>
                <span x-show="sidebarOpen" class="truncate">{{ __('super_admin.nav.notifications') }}</span>
                <div x-show="!sidebarOpen" class="absolute left-full ml-2 hidden rounded-md bg-slate-900 px-2 py-1 text-xs text-white opacity-0 group-hover:block group-hover:opacity-100 z-50 whitespace-nowrap shadow-xl">
                    {{ __('super_admin.nav.notifications') }}
                </div>
            </a>

            <!-- Monitoring (super_admin only) -->
            @if(auth()->user()->canPlatformAdminister())
            <a
                href="{{ route('platform-admin.monitoring') }}"
                class="group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200 {{ $isSaMonitoring ? 'text-white bg-white/10 shadow-sm ring-1 ring-white/5' : 'text-white/60 hover:bg-white/5 hover:text-white' }}"
                :class="sidebarOpen ? '' : 'justify-center'"
            >
                @if($isSaMonitoring)
                    <div class="absolute left-0 h-6 w-1 rounded-r-full bg-[#F2E3BB] shadow-[0_0_10px_rgba(242,227,187,0.6)]" x-show="sidebarOpen"></div>
                @endif
                <iconify-icon icon="solar:monitor-bold-duotone" width="20" class="{{ $isSaMonitoring ? 'text-[#F2E3BB]' : 'text-white/50 group-hover:text-white/80' }} transition-colors"></iconify-icon>
                <span x-show="sidebarOpen" class="truncate">{{ __('super_admin.nav.monitoring') }}</span>
                <div x-show="!sidebarOpen" class="absolute left-full ml-2 hidden rounded-md bg-slate-900 px-2 py-1 text-xs text-white opacity-0 group-hover:block group-hover:opacity-100 z-50 whitespace-nowrap shadow-xl">
                    {{ __('super_admin.nav.monitoring') }}
                </div>
            </a>

            <!-- Backups (super_admin only) -->
            <a
                href="{{ route('platform-admin.backups') }}"
                class="group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200 {{ $isSaBackups ? 'text-white bg-white/10 shadow-sm ring-1 ring-white/5' : 'text-white/60 hover:bg-white/5 hover:text-white' }}"
                :class="sidebarOpen ? '' : 'justify-center'"
            >
                @if($isSaBackups)
                    <div class="absolute left-0 h-6 w-1 rounded-r-full bg-[#F2E3BB] shadow-[0_0_10px_rgba(242,227,187,0.6)]" x-show="sidebarOpen"></div>
                @endif
                <iconify-icon icon="solar:database-bold-duotone" width="20" class="{{ $isSaBackups ? 'text-[#F2E3BB]' : 'text-white/50 group-hover:text-white/80' }} transition-colors"></iconify-icon>
                <span x-show="sidebarOpen" class="truncate">{{ __('super_admin.nav.backups') }}</span>
                <div x-show="!sidebarOpen" class="absolute left-full ml-2 hidden rounded-md bg-slate-900 px-2 py-1 text-xs text-white opacity-0 group-hover:block group-hover:opacity-100 z-50 whitespace-nowrap shadow-xl">
                    {{ __('super_admin.nav.backups') }}
                </div>
            </a>
            @endif

            <!-- Section: Support -->
            <div class="px-3 mb-2 mt-4 text-[10px] font-bold uppercase tracking-widest text-white/30 transition-opacity duration-300" x-show="sidebarOpen">
                {{ __('super_admin.nav.section_support') }}
            </div>

            <!-- Support Sessions -->
            <a
                href="{{ route('platform-admin.support-sessions') }}"
                class="group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200 {{ $isSaSupport ? 'text-white bg-white/10 shadow-sm ring-1 ring-white/5' : 'text-white/60 hover:bg-white/5 hover:text-white' }}"
                :class="sidebarOpen ? '' : 'justify-center'"
            >
                @if($isSaSupport)
                    <div class="absolute left-0 h-6 w-1 rounded-r-full bg-[#F2E3BB] shadow-[0_0_10px_rgba(242,227,187,0.6)]" x-show="sidebarOpen"></div>
                @endif
                <iconify-icon icon="solar:headphones-round-bold-duotone" width="20" class="{{ $isSaSupport ? 'text-[#F2E3BB]' : 'text-white/50 group-hover:text-white/80' }} transition-colors"></iconify-icon>
                <span x-show="sidebarOpen" class="truncate">{{ __('super_admin.nav.support_sessions') }}</span>
                <div x-show="!sidebarOpen" class="absolute left-full ml-2 hidden rounded-md bg-slate-900 px-2 py-1 text-xs text-white opacity-0 group-hover:block group-hover:opacity-100 z-50 whitespace-nowrap shadow-xl">
                    {{ __('super_admin.nav.support_sessions') }}
                </div>
            </a>

        </div>

        <!-- Collapse Button -->
        <button
            type="button"
            class="absolute -right-3 top-20 hidden md:flex h-6 w-6 items-center justify-center rounded-full bg-white text-slate-400 shadow-md ring-1 ring-slate-100 hover:text-[#005F02] transition-colors z-50"
            @click="
                sidebarOpen = !sidebarOpen;
                const offset = sidebarOpen ? 'var(--manexo-sidebar-expanded)' : 'var(--manexo-sidebar-collapsed)';
                document.body.style.setProperty('--manexo-shell-offset', offset);
                localStorage.setItem('sa_sidebar', sidebarOpen ? 'true' : 'false');
            "
        >
            <iconify-icon :icon="sidebarOpen ? 'solar:alt-arrow-left-linear' : 'solar:alt-arrow-right-linear'" width="14"></iconify-icon>
        </button>
    </aside>

    <!-- TOPBAR -->
    <header
        class="manexo-shell-transition fixed top-0 right-0 z-30 flex h-14 sm:h-16 items-center justify-between border-b border-slate-200/60 bg-white/80 backdrop-blur-lg px-4 sm:px-6 transition-[left] duration-300 ease-[cubic-bezier(0.25,1,0.5,1)] md:left-[var(--manexo-shell-offset)] left-0"
    >
        <div class="flex items-center gap-3">
            <!-- Mobile hamburger -->
            <button type="button" class="md:hidden flex items-center justify-center h-9 w-9 rounded-lg hover:bg-slate-100 transition-colors" @click="mobileOpen = !mobileOpen">
                <iconify-icon icon="solar:hamburger-menu-linear" width="20" class="text-slate-500"></iconify-icon>
            </button>
            <h1 class="text-base font-semibold text-slate-800">{{ __($title ?? 'super_admin.title') }}</h1>
        </div>

        <div class="flex items-center gap-3">
            <!-- User dropdown -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex items-center gap-2 rounded-lg px-2.5 py-1.5 hover:bg-slate-100 transition-colors">
                    <div class="h-8 w-8 rounded-full bg-[#F2E3BB]/30 flex items-center justify-center">
                        <span class="text-xs font-bold text-[#005F02]">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                    </div>
                    <span class="hidden sm:block text-sm font-medium text-slate-700">{{ auth()->user()->name }}</span>
                    <iconify-icon icon="solar:alt-arrow-down-linear" width="12" class="text-slate-400"></iconify-icon>
                </button>

                <div x-show="open" @click.away="open = false" x-transition
                     class="absolute right-0 mt-2 w-48 rounded-xl bg-white shadow-xl ring-1 ring-slate-200/60 py-1 z-50">
                    <a href="{{ route('platform-admin.profile') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
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
        class="manexo-shell-transition flex-1 flex flex-col min-w-0 min-h-0 w-full max-w-full relative z-10 transition-[padding] duration-300 ease-[cubic-bezier(0.25,1,0.5,1)] pt-14 sm:pt-16 pl-0 md:pl-[var(--manexo-shell-offset)]"
    >
        <div class="page-content-safe manexo-shell-scroll flex-1 min-h-0 overflow-y-auto overflow-x-auto custom-scrollbar">
            <div class="mx-auto manexo-content-wrap animate-enter space-y-[var(--manexo-space-section)]">
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
</body>
</html>
