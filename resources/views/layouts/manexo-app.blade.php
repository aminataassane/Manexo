<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full antialiased bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, maximum-scale=5">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ is_string($title ?? null) ? __($title) : 'Manexo' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Logo(1).png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <script defer src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Mona+Sans:ital,wght@0,200..900;1,200..900&display=swap" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Mona+Sans:ital,wght@0,200..900;1,200..900&display=swap"></noscript>

    <style>
        /* Typography: Mona Sans uniquement */
        body, .font-serif {
            font-family: "Mona Sans", sans-serif;
            font-optical-sizing: auto;
            font-style: normal;
            font-variation-settings: "wdth" 100;
        }
        [x-cloak] { display: none !important; }
        html { overflow-x: hidden; }

        /* Animations (tickets / discussions ; pas sur le layout pour éviter 0,5s à chaque wire:navigate) */
        @keyframes subtleFade { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: translateY(0); } }
        .animate-enter { animation: subtleFade 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        @media (prefers-reduced-motion: reduce) {
            .animate-enter { animation: none; opacity: 1; transform: none; }
        }
        html.manexo-is-navigating .manexo-app-main-scroll { pointer-events: none; }
        html.manexo-is-navigating .manexo-content-wrap {
            opacity: 0.93;
            transform: translateY(1px);
            transition: opacity 0.06s ease-out, transform 0.06s ease-out;
        }
        @media (prefers-reduced-motion: reduce) {
            html.manexo-is-navigating .manexo-content-wrap { opacity: 1; transform: none; transition: none; }
        }

        /* Livewire navigate progress bar */
        [x-ref="progressBar"] {
            height: 3px !important;
            background-color: var(--accent) !important;
            box-shadow: 0 0 8px var(--accent), 0 0 2px var(--accent) !important;
            transition: width 0.1s ease !important;
        }

        /* Organization accent color */
        ::selection { background: var(--accent-soft); color: var(--accent); }
        /* En sidebar repliée, on neutralise les mini-tooltips (évite texte qui déborde) */
        aside .group > .absolute.left-full { display: none !important; }

        /* ─── Print: hide shell UI, full-width content ─── */
        @media print {
            body { overflow: visible !important; height: auto !important; display: block !important; }
            aside, header,
            .fixed.inset-0,                 /* mobile overlay */
            .pointer-events-none.fixed      /* bg safety layer */
            { display: none !important; }
            main {
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                position: static !important;
            }
            .page-content-safe {
                overflow: visible !important;
                padding: 10px !important;
            }
        }
    </style>
</head>
@php
    // currentOrganization is injected by EnsureOrganizationIsSelected middleware.
    $currentOrganization = $currentOrganization ?? request()->attributes->get('currentOrganization');
    $defaultAccent = env('MANEXO_DEFAULT_ACCENT', '#005F02');
    $accent = ($currentOrganization?->primary_color ?: $defaultAccent);
@endphp
<body
    class="manexo-fluid-root flex h-screen w-full min-h-0 overflow-hidden bg-slate-50 text-slate-900"
    data-echo-enabled="1"
    style="
        --accent: {{ $accent }};
        --accent-soft: color-mix(in srgb, var(--accent) 15%, white);
        --accent-soft-2: color-mix(in srgb, var(--accent) 25%, white);
        --accent-dark: color-mix(in srgb, var(--accent) 20%, black);
        --accent-ring: color-mix(in srgb, var(--accent) 20%, transparent);
        --livewire-progress-bar-color: var(--accent);
    "
    x-data="{ sidebarOpen: true, mobileOpen: false }"
    x-init="sidebarOpen = (localStorage.getItem('manexo_sidebar') !== 'false'); document.body.style.setProperty('--manexo-shell-offset', sidebarOpen ? 'var(--manexo-sidebar-expanded)' : 'var(--manexo-sidebar-collapsed)'); document.addEventListener('livewire:navigated', () => { mobileOpen = false })"
    x-effect="localStorage.setItem('manexo_sidebar', sidebarOpen); document.body.style.setProperty('--manexo-shell-offset', sidebarOpen ? 'var(--manexo-sidebar-expanded)' : 'var(--manexo-sidebar-collapsed)')"
>

    <!-- Support Session Banner -->
    @if (isset($activeSupportSession) && $activeSupportSession)
        <div
            class="fixed top-0 left-0 right-0 z-[60] bg-amber-500 text-white text-center py-2 px-4 text-sm font-medium shadow-lg"
            x-data="{
                expiresAt: new Date('{{ $activeSupportSession->expires_at->toIso8601String() }}').getTime(),
                remaining: '',
                expired: false,
                init() {
                    this.tick();
                    setInterval(() => this.tick(), 1000);
                },
                tick() {
                    const diff = this.expiresAt - Date.now();
                    if (diff <= 0) {
                        this.expired = true;
                        this.remaining = '0:00';
                        window.location.href = '{{ route('platform-admin.support-sessions') }}';
                        return;
                    }
                    const mins = Math.floor(diff / 60000);
                    const secs = Math.floor((diff % 60000) / 1000);
                    this.remaining = mins + ':' + String(secs).padStart(2, '0');
                }
            }"
        >
            <div class="flex items-center justify-center gap-3">
                <iconify-icon icon="solar:headphones-round-bold" width="16"></iconify-icon>
                <span>
                    {{ __('super_admin.support.active_banner_org', ['org' => $activeSupportSession->organization?->name]) }}
                    —
                    <span x-text="remaining"></span>
                </span>
                <a href="{{ route('platform-admin.support-sessions') }}" class="ml-2 rounded-lg bg-white/20 px-3 py-1 text-xs font-semibold hover:bg-white/30 transition-colors">
                    {{ __('super_admin.support.end') }}
                </a>
            </div>
        </div>
    @endif

    <!-- Safety: ensure main app background is never tinted by branding -->
    <div class="pointer-events-none fixed inset-0 -z-10 bg-slate-50"></div>

    <!-- Mobile Overlay -->
    <div x-show="mobileOpen" x-transition.opacity class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm md:hidden" @click="mobileOpen = false"></div>

    {{-- Shell conservé entre les navigations wire:navigate (pas de rechargement visuel du menu / header) --}}
    @persist('manexo-sidebar')
        <x-manexo.sidebar />
    @endpersist

    @persist('manexo-topbar')
        <x-manexo.topbar />
    @endpersist

    <!-- MAIN CONTENT: pt = hauteur du header (topbar) pour que le contenu reste sous le topbar -->
        {{-- Décalage = --manexo-shell-offset (fluide + synchronisé barre / header via Alpine sur body) --}}
        <main
            class="manexo-shell-transition flex-1 flex flex-col min-w-0 min-h-0 w-full max-w-full relative z-10 transition-[padding] duration-300 ease-[cubic-bezier(0.25,1,0.5,1)] {{ isset($activeSupportSession) && $activeSupportSession ? 'pt-24 sm:pt-[6.5rem]' : 'pt-14 sm:pt-16' }} pl-0 md:pl-[var(--manexo-shell-offset)]"
    >
        <!-- PAGE BODY (scrollable) -->
        <div class="manexo-app-main-scroll page-content-safe manexo-shell-scroll flex-1 min-h-0 overflow-y-auto overflow-x-hidden custom-scrollbar">
            <div class="mx-auto manexo-content-wrap space-y-[var(--manexo-space-section)]">
                {{ $slot }}
            </div>
        </div>
    </main>

    {{-- Global toast & confirm dialog --}}
    <x-manexo.toast />
    <x-manexo.confirm-dialog />

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
    <script>
        document.addEventListener('livewire:init', function() {
            // Bridge Livewire dispatch('toast') → Alpine window event
            Livewire.on('toast', function(params) {
                var p = Array.isArray(params) ? params[0] : params;
                window.dispatchEvent(new CustomEvent('toast', { detail: p }));
            });
            // Couleur d’accent org : le layout n’est pas re-rendu par Livewire après « Enregistrer »
            Livewire.on('manexo-accent', function(params) {
                var p = Array.isArray(params) ? params[0] : params;
                var accent = p && p.accent ? p.accent : null;
                if (!accent) {
                    return;
                }
                document.body.style.setProperty('--accent', accent);
                document.body.style.setProperty('--accent-soft', 'color-mix(in srgb, ' + accent + ' 15%, white)');
                document.body.style.setProperty('--accent-soft-2', 'color-mix(in srgb, ' + accent + ' 25%, white)');
                document.body.style.setProperty('--accent-dark', 'color-mix(in srgb, ' + accent + ' 20%, black)');
                document.body.style.setProperty('--accent-ring', 'color-mix(in srgb, ' + accent + ' 20%, transparent)');
            });
        });
    </script>
</body>
</html>
