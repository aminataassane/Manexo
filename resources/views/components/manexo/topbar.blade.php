@php
    $user = Auth::user();
    // currentOrganization is injected by EnsureOrganizationIsSelected middleware (includes pivot role).
    $org = $currentOrganization ?? request()->attributes->get('currentOrganization');
    $role = (string) ($org?->pivot?->role ?? 'member');

    $orgName = $org?->name ?? 'Sélectionner';
    $orgInitial = $orgName ? mb_strtoupper(mb_substr($orgName, 0, 1)) : '—';

    $roleLabel = match ((string) $role) {
        'owner' => __('Owner'),
        'admin' => __('Admin'),
        'agent' => __('Agent'),
        default => __('Member'),
    };
@endphp

<!-- TOPBAR: White background for a clean look -->
<header
    class="manexo-shell-transition fixed top-0 left-0 right-0 z-40 flex h-14 sm:h-16 shrink-0 items-center justify-between border-b border-slate-200 bg-white shadow-sm backdrop-blur-md px-3 transition-[left] duration-300 ease-[cubic-bezier(0.25,1,0.5,1)] sm:px-6 md:left-[var(--manexo-shell-offset)] min-[1920px]:pl-4"
    style="padding-left: max(0.75rem, env(safe-area-inset-left)); padding-top: env(safe-area-inset-top, 0);"
>
    <!-- Left: Mobile Menu + Search -->
    <div class="flex flex-1 items-center gap-4">
        <!-- Mobile Toggle -->
        <button
            @click="mobileOpen = !mobileOpen"
            class="flex h-10 w-10 items-center justify-center rounded-xl text-slate-500 hover:bg-slate-100 hover:text-slate-900 md:hidden transition-colors"
        >
            <iconify-icon icon="solar:hamburger-menu-linear" width="24"></iconify-icon>
        </button>

        <!-- Search Bar -->
        <div class="hidden max-w-md flex-1 md:block">
            <div class="relative group">
                <label for="topbar_search" class="sr-only">Rechercher</label>
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <iconify-icon icon="solar:magnifer-linear" class="text-slate-400 group-focus-within:text-[var(--accent)] transition-colors" width="18"></iconify-icon>
                </div>
                <input
                    type="text"
                    id="topbar_search"
                    name="topbar_search"
                    placeholder="Rechercher un ticket, un client..."
                    class="block w-full rounded-xl border-0 bg-slate-50 py-2.5 pl-10 pr-3 text-sm text-slate-900 ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-[var(--accent)] transition-all hover:bg-slate-100 focus:bg-white"
                >
                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                    {{-- <span class="text-xs text-slate-400 border border-slate-200 rounded px-1.5 py-0.5">⌘K</span> --}}
                </div>
            </div>
        </div>
    </div>

    <!-- Right: Organization + Notifications + Profile -->
    <div class="flex items-center gap-3 sm:gap-4">



        <!-- Notifications -->
        <livewire:notifications-bell />

        <!-- User Profile -->
        <div class="relative ml-2" x-data="{ open: false }">
            <button
                @click="open = !open"
                @click.outside="open = false"
                class="flex items-center gap-3 rounded-full transition-opacity hover:opacity-80 focus:outline-none"
            >
                @php
                    $name = (string) ($user?->name ?? 'U');
                    $initials = trim(mb_strtoupper(mb_substr($name, 0, 1)));
                @endphp
                <div class="h-9 w-9 rounded-full shadow-sm ring-2 ring-white flex items-center justify-center text-sm font-extrabold"
                     style="background: var(--accent-soft); color: var(--accent);">
                    {{ $initials ?: 'U' }}
                </div>
                <div class="hidden text-left md:block">
                    <p class="text-sm font-semibold text-slate-900 leading-none">{{ $user?->name }}</p>
                    <p class="text-xs text-slate-500 mt-0.5 leading-none">{{ $roleLabel }}</p>
                </div>
                <iconify-icon icon="solar:alt-arrow-down-linear" class="text-slate-400 hidden md:block" width="12"></iconify-icon>
            </button>

            <!-- Dropdown Menu -->
            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="absolute right-0 mt-2 w-56 max-w-[calc(100vw-2rem)] origin-top-right rounded-xl bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
                style="display: none;"
            >
                <div class="px-4 py-3 border-b border-slate-100">
                    <p class="text-sm font-medium text-slate-900">{{ __('menu.logged_in_as') }}</p>
                    <p class="truncate text-sm text-slate-500">{{ $user?->email }}</p>
                </div>

                <div class="py-1">
                    <a href="{{ route('profile') }}" wire:navigate.hover class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                        <iconify-icon icon="solar:user-linear" width="16"></iconify-icon>
                        {{ __('menu.my_profile') }}
                    </a>
                    <a href="{{ route('organizations.select', ['mode' => 'switch']) }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                        <iconify-icon icon="solar:buildings-linear" width="16"></iconify-icon>
                        {{ __('menu.switch_company') }}
                    </a>
                </div>

                <div class="border-t border-slate-100 py-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                            <iconify-icon icon="solar:logout-2-linear" width="16"></iconify-icon>
                            {{ __('menu.logout') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
