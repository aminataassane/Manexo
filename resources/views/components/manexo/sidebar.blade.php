@php
    $org = request()->attributes->get('currentOrganization') ?? \App\Models\Organization::find(session('current_organization_id'));
    $user = auth()->user();
    $orgRole = ($org && $user) ? ($user->organizations()->whereKey($org->id)->first()?->pivot?->role ?? 'member') : 'member';
    $isStaff = in_array($orgRole, ['owner', 'admin'], true);
@endphp

<aside
    class="fixed top-0 left-0 z-50 h-full flex flex-col bg-white border-r border-[#E5E7EB] text-[#6B7280] shrink-0 transition-all duration-300 ease-in-out"
    :class="{
        'w-[220px]': sidebarOpen,
        'w-[72px]': !sidebarOpen,
        '-translate-x-full md:translate-x-0': !mobileOpen,
        'translate-x-0': mobileOpen
    }"
>
    <!-- Brand Logo -->
    <div class="h-14 flex items-center px-3 border-b border-[#E5E7EB] gap-2">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 flex-1 overflow-hidden" :class="sidebarOpen ? '' : 'justify-center'">
            <div class="w-6 h-6 rounded flex items-center justify-center shadow-sm bg-[color:var(--accent)]">
                <iconify-icon icon="solar:bolt-linear" width="16" class="text-white"></iconify-icon>
            </div>
            <span class="font-bold text-sm tracking-tight text-[#111827] whitespace-nowrap" x-show="sidebarOpen">MANEXO</span>
        </a>

        <!-- Collapse (desktop) -->
        <button
            type="button"
            class="hidden md:flex h-8 w-8 items-center justify-center rounded-md text-[#6B7280] hover:text-[#111827] hover:bg-[#F9FAFB] transition-colors border border-transparent hover:border-[#E5E7EB]"
            @click="sidebarOpen = !sidebarOpen"
            :title="sidebarOpen ? 'Réduire le menu' : 'Ouvrir le menu'"
        >
            <iconify-icon :icon="sidebarOpen ? 'solar:alt-arrow-left-linear' : 'solar:alt-arrow-right-linear'" width="16"></iconify-icon>
        </button>
    </div>

    <!-- Navigation -->
    <div class="flex-1 overflow-y-auto custom-scrollbar pt-4 pr-2 pb-4 pl-2 space-y-0.5">
        <div class="px-2 mb-2 text-[10px] font-medium text-[#6B7280] uppercase tracking-wider" x-show="sidebarOpen">Menu</div>

        @php
            $isDashboard = request()->routeIs('dashboard');
            $isTickets = request()->routeIs('tickets.*');
        @endphp

        <!-- Dashboard -->
        <a
            href="{{ route('dashboard') }}"
            class="flex items-center gap-2.5 px-2 py-1.5 text-[13px] font-medium rounded-md transition-colors border border-transparent"
            :class="sidebarOpen ? '' : 'justify-center'"
            style="{{ $isDashboard ? 'color: var(--accent); background: var(--accent-soft); border-color: var(--accent-soft);' : '' }}"
        >
            <iconify-icon icon="solar:widget-2-linear" width="16"></iconify-icon>
            <span x-show="sidebarOpen">Tableau de bord</span>
        </a>

        <!-- Tickets -->
        <a
            href="{{ route('tickets.index') }}"
            class="flex items-center gap-2.5 px-2 py-1.5 text-[13px] font-medium rounded-md transition-colors"
            :class="sidebarOpen ? '' : 'justify-center'"
            style="{{ $isTickets ? 'color: var(--accent); background: var(--accent-soft); border-color: var(--accent-soft); border-width: 1px;' : '' }}"
        >
            <iconify-icon icon="solar:ticket-linear" width="16"></iconify-icon>
            <span x-show="sidebarOpen">Tickets</span>
        </a>

        <!-- Discussions (placeholder) -->
        <a
            href="#"
            class="flex items-center gap-2.5 px-2 py-1.5 text-[13px] font-medium text-[#6B7280] hover:text-[#111827] hover:bg-[#F9FAFB] rounded-md transition-colors"
            :class="sidebarOpen ? '' : 'justify-center'"
        >
            <iconify-icon icon="solar:chat-round-linear" width="16"></iconify-icon>
            <span x-show="sidebarOpen">Discussions</span>
        </a>

        <!-- Clients (placeholder) -->
        <a
            href="#"
            class="flex items-center gap-2.5 px-2 py-1.5 text-[13px] font-medium text-[#6B7280] hover:text-[#111827] hover:bg-[#F9FAFB] rounded-md transition-colors"
            :class="sidebarOpen ? '' : 'justify-center'"
        >
            <iconify-icon icon="solar:users-group-rounded-linear" width="16"></iconify-icon>
            <span x-show="sidebarOpen">Clients</span>
        </a>

        @if ($isStaff)
            <div class="px-2 mt-6 mb-2 text-[10px] font-medium text-[#6B7280] uppercase tracking-wider" x-show="sidebarOpen">Admin</div>

            @php
                $isAdminUsers = request()->routeIs('admin.users');
            @endphp

            <!-- Équipe / Utilisateurs -->
            <a
                href="{{ route('admin.users') }}"
                class="flex items-center gap-2.5 px-2 py-1.5 text-[13px] font-medium rounded-md transition-colors border border-transparent"
                :class="sidebarOpen ? '' : 'justify-center'"
                style="{{ $isAdminUsers ? 'color: var(--accent); background: var(--accent-soft); border-color: var(--accent-soft);' : '' }}"
            >
                <iconify-icon icon="solar:users-group-rounded-linear" width="16"></iconify-icon>
                <span x-show="sidebarOpen">Équipe</span>
            </a>

            @php
                $isReports = request()->routeIs('reports.*');
            @endphp

            <!-- Rapports -->
            <a
                href="{{ route('reports.index') }}"
                class="flex items-center gap-2.5 px-2 py-1.5 text-[13px] font-medium rounded-md transition-colors border border-transparent"
                :class="sidebarOpen ? '' : 'justify-center'"
                style="{{ $isReports ? 'color: var(--accent); background: var(--accent-soft); border-color: var(--accent-soft);' : '' }}"
            >
                <iconify-icon icon="solar:chart-2-linear" width="16"></iconify-icon>
                <span x-show="sidebarOpen">Rapports</span>
            </a>

            <!-- Paramètres (placeholder) -->
            <a
                href="{{ route('admin.settings') }}"
                class="flex items-center gap-2.5 px-2 py-1.5 text-[13px] font-medium rounded-md transition-colors border border-transparent"
                :class="sidebarOpen ? '' : 'justify-center'"
                style="{{ request()->routeIs('admin.settings') ? 'color: var(--accent); background: var(--accent-soft); border-color: var(--accent-soft);' : '' }}"
            >
                <iconify-icon icon="solar:settings-linear" width="16"></iconify-icon>
                <span x-show="sidebarOpen">Paramètres</span>
            </a>
        @endif
    </div>

    <!-- Sidebar Bottom Footer -->
    <div class="p-4 border-t border-[#E5E7EB] text-[10px] text-[#6B7280] text-center" x-show="sidebarOpen">
        {{ $org?->name ? 'Espace : '.$org->name : 'Sélectionner une entreprise' }}
    </div>
</aside>
