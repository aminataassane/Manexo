@php
    $user = Auth::user();
    $org = request()->attributes->get('currentOrganization') ?? \App\Models\Organization::find(session('current_organization_id'));

    $role = 'member';
    if ($org && $user) {
        $role = $user->organizations()->whereKey($org->id)->first()?->pivot?->role ?: 'member';
    }

    $orgName = $org?->name ?? 'Sélectionner';
    $orgInitial = $orgName ? mb_strtoupper(mb_substr($orgName, 0, 1)) : '—';
@endphp

<!-- TOPBAR (Context Global) -->
<header
    class="flex sticky z-30 h-14 border-[#E5E7EB] border-b px-4 sm:px-6 top-0 backdrop-blur-md items-center justify-between w-full overflow-x-hidden"
    style="background-color: color-mix(in srgb, var(--accent) 10%, white);"
>
    <!-- Left: sidebar toggle + Company Selector & Search -->
    <div class="flex items-center gap-3 sm:gap-4 flex-1 min-w-0">
        <!-- Desktop Sidebar Toggle -->
        <button
            @click="sidebarOpen = !sidebarOpen"
            class="hidden md:flex h-8 w-8 items-center justify-center text-[#6B7280] hover:text-[#111827] hover:bg-white/60 rounded-md transition-colors border border-transparent hover:border-[#E5E7EB]"
            title="Ouvrir / fermer le menu"
        >
            <iconify-icon icon="solar:hamburger-menu-linear" width="18"></iconify-icon>
        </button>

        <!-- Mobile Sidebar Toggle -->
        <button
            @click="mobileOpen = !mobileOpen"
            class="md:hidden flex h-8 w-8 items-center justify-center text-[#6B7280] hover:text-[#111827] hover:bg-white/60 rounded-md transition-colors border border-transparent hover:border-[#E5E7EB]"
            title="Menu"
        >
            <iconify-icon icon="solar:hamburger-menu-linear" width="18"></iconify-icon>
        </button>

        <!-- Company Selector -->
        <a
            href="{{ route('organizations.select') }}"
            class="flex items-center gap-2 pl-1 pr-2 py-1 hover:bg-white/60 rounded-md transition-colors border border-transparent hover:border-[#E5E7EB] max-w-[220px] min-w-0"
            title="Changer d'entreprise"
        >
            <div class="w-5 h-5 rounded flex items-center justify-center font-bold text-[10px]" style="background: var(--accent-soft); color: var(--accent);">
                {{ $orgInitial }}
            </div>
            <span class="text-[13px] font-medium text-[#111827] truncate min-w-0">{{ $orgName }}</span>
            <iconify-icon icon="solar:alt-arrow-down-linear" class="text-[#6B7280]" width="12"></iconify-icon>
        </a>

        <div class="hidden sm:block h-4 w-px bg-[#E5E7EB]"></div>

        <!-- Search -->
        <div class="relative flex-1 min-w-0 max-w-sm group hidden sm:block">
            <iconify-icon
                icon="solar:magnifer-linear"
                class="absolute left-2.5 top-1/2 -translate-y-1/2 text-[#6B7280] transition-colors"
                width="14"
                style="color: color-mix(in srgb, var(--accent) 30%, #6B7280);"
            ></iconify-icon>
            <input
                type="text"
                placeholder="Rechercher un ticket (ID, sujet)..."
                class="w-full h-8 pl-8 pr-3 bg-[#F9FAFB] border border-[#E5E7EB] rounded-md text-[13px] text-[#111827] placeholder:text-[#6B7280] focus:outline-none transition-all min-w-0"
                style="--tw-ring-color: var(--accent-ring);"
                onfocus="this.style.borderColor=getComputedStyle(document.body).getPropertyValue('--accent')"
                onblur="this.style.borderColor='#E5E7EB'"
            >
        </div>
    </div>

    <!-- Right: Notifs & User Profile -->
    <div class="flex items-center gap-2 sm:gap-3 pl-3 sm:pl-4 shrink-0">
        <button class="relative h-8 w-8 flex items-center justify-center text-[#6B7280] hover:text-[#111827] hover:bg-white/60 rounded-full transition-colors" title="Notifications">
            <iconify-icon icon="solar:bell-linear" width="18"></iconify-icon>
            <span class="absolute top-2 right-2 w-1.5 h-1.5 bg-red-500 border border-white rounded-full"></span>
        </button>

        <div class="h-4 w-px bg-[#E5E7EB]"></div>

        <div class="flex items-center gap-2 hover:bg-white/60 rounded-full pl-1 pr-2 py-1 transition-colors min-w-0">
            <img
                src="https://ui-avatars.com/api/?name={{ urlencode($user?->name ?? 'User') }}&background=F3F4F6&color=111827"
                alt="User"
                class="w-6 h-6 rounded-full bg-[#F9FAFB] ring-1 ring-[#E5E7EB]"
            >
            <div class="hidden sm:block text-left">
                <p class="text-[12px] font-medium text-[#111827] leading-none">{{ $user?->name }}</p>
                <p class="text-[10px] text-[#6B7280] leading-none mt-0.5">{{ ucfirst($role) }}</p>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="ml-1">
                @csrf
                <button type="submit" class="text-[#6B7280] hover:text-red-600 transition-colors" title="Déconnexion">
                    <iconify-icon icon="solar:logout-2-linear" width="14"></iconify-icon>
                </button>
            </form>
        </div>
    </div>
</header>