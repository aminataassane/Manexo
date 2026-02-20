@php
    // currentOrganization is injected by EnsureOrganizationIsSelected middleware (includes pivot role).
    $org = $currentOrganization ?? request()->attributes->get('currentOrganization');
    $orgRole = (string) ($org?->pivot?->role ?? 'member');
    // Staff = can access internal pages (agent included).
    $isStaff = in_array($orgRole, ['owner', 'admin', 'agent'], true);
@endphp

<aside
    class="fixed left-0 top-0 z-40 flex h-screen shrink-0 flex-col border-r border-white/5 text-white/60 transition-all duration-300 ease-[cubic-bezier(0.25,1,0.5,1)] max-w-[85vw] md:max-w-none backdrop-blur-xl"
    style="background: linear-gradient(180deg, var(--accent-dark) 0%, color-mix(in srgb, var(--accent-dark) 90%, black) 100%);"
    :class="{
        'w-[240px] xl:w-[260px]': sidebarOpen,
        'w-[72px] xl:w-[80px]': !sidebarOpen,
        '-translate-x-full md:translate-x-0': !mobileOpen,
        'translate-x-0': mobileOpen
    }"
>
    <!-- LOGO AREA -->
    <div class="flex h-16 shrink-0 items-center px-4 xl:px-5" :class="sidebarOpen ? 'justify-start' : 'justify-center'">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group transition-all duration-300">
            <div class="relative flex h-9 w-9 shrink-0 items-center justify-center rounded-xl font-bold text-sm shadow-lg shadow-[var(--accent-ring)] ring-1 ring-white/10 group-hover:scale-105 transition-transform" 
                 style="background: linear-gradient(135deg, var(--accent-soft), var(--accent)); color: var(--accent-dark);">
                M
                <div class="absolute inset-0 rounded-xl bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            </div>
            <div class="flex flex-col" x-show="sidebarOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0">
                <span class="text-sm font-bold text-white tracking-wide">MANEXO</span>
                <span class="text-[10px] font-medium text-white/40 uppercase tracking-widest">Helpdesk</span>
            </div>
        </a>
    </div>

    <!-- Navigation -->
    <div class="custom-scrollbar flex flex-1 flex-col gap-1 overflow-y-auto px-3 py-4">
        
        <!-- Section Label -->
        <div class="px-3 mb-2 mt-2 text-[10px] font-bold uppercase tracking-widest text-white/30 transition-opacity duration-300" x-show="sidebarOpen">
            Menu Principal
        </div>

        @php
            $isDashboard = request()->routeIs('dashboard');
            $isTickets = request()->routeIs('tickets.*');
            $isDiscussions = request()->routeIs('discussions.*');
            $isForms = request()->routeIs('forms.*');
            $currentDisplayMode = request()->query('displayMode', 'list');
        @endphp

        <!-- Dashboard -->
        <a
            href="{{ route('dashboard') }}"
            class="group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200 {{ $isDashboard ? 'text-white bg-white/10 shadow-sm ring-1 ring-white/5' : 'text-white/60 hover:bg-white/5 hover:text-white' }}"
            :class="sidebarOpen ? '' : 'justify-center'"
        >
            @if($isDashboard)
                <div class="absolute left-0 h-6 w-1 rounded-r-full bg-[var(--accent)] shadow-[0_0_10px_var(--accent)]" x-show="sidebarOpen"></div>
            @endif
            <iconify-icon icon="solar:widget-5-bold-duotone" width="20" class="{{ $isDashboard ? 'text-[var(--accent-soft)]' : 'text-white/50 group-hover:text-white/80' }} transition-colors"></iconify-icon>
            <span x-show="sidebarOpen" class="truncate">Tableau de bord</span>
            
            <!-- Tooltip for collapsed state -->
            <div x-show="!sidebarOpen" class="absolute left-full ml-2 hidden rounded-md bg-slate-900 px-2 py-1 text-xs text-white opacity-0 group-hover:block group-hover:opacity-100 z-50 whitespace-nowrap shadow-xl">
                Tableau de bord
            </div>
        </a>

        <!-- Tickets -->
        <div x-data="{ ticketsOpen: {{ $isTickets ? 'true' : 'false' }} }">
            <div x-show="sidebarOpen">
                <button
                    type="button"
                    @click="ticketsOpen = !ticketsOpen"
                    class="group flex w-full items-center justify-between rounded-xl px-3 py-2.5 text-left text-sm font-medium transition-all duration-200 {{ $isTickets ? 'text-white' : 'text-white/60 hover:bg-white/5 hover:text-white' }}"
                >
                    <div class="flex items-center gap-3">
                        <iconify-icon icon="solar:ticket-bold-duotone" width="20" class="{{ $isTickets ? 'text-[var(--accent-soft)]' : 'text-white/50 group-hover:text-white/80' }} transition-colors"></iconify-icon>
                        <span>Tickets</span>
                    </div>
                    <iconify-icon :icon="ticketsOpen ? 'solar:alt-arrow-up-linear' : 'solar:alt-arrow-down-linear'" width="12" class="opacity-50 transition-transform duration-200" :class="ticketsOpen ? 'rotate-0' : '-rotate-90'"></iconify-icon>
                </button>
                
                <div x-show="ticketsOpen" x-collapse class="mt-1 space-y-1 px-3">
                    <a
                        href="{{ route('tickets.index', ['displayMode' => 'list']) }}"
                        class="flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-medium transition-colors {{ $isTickets && $currentDisplayMode === 'list' ? 'bg-white/10 text-[var(--accent-soft)]' : 'text-white/40 hover:text-white hover:bg-white/5' }}"
                    >
                        <div class="h-1.5 w-1.5 rounded-full {{ $isTickets && $currentDisplayMode === 'list' ? 'bg-[var(--accent)]' : 'bg-white/20' }}"></div>
                        {{ __('Vue Liste') }}
                    </a>
                    <a
                        href="{{ route('tickets.index', ['displayMode' => 'kanban']) }}"
                        class="flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-medium transition-colors {{ $isTickets && $currentDisplayMode === 'kanban' ? 'bg-white/10 text-[var(--accent-soft)]' : 'text-white/40 hover:text-white hover:bg-white/5' }}"
                    >
                        <div class="h-1.5 w-1.5 rounded-full {{ $isTickets && $currentDisplayMode === 'kanban' ? 'bg-[var(--accent)]' : 'bg-white/20' }}"></div>
                        {{ __('Vue Kanban') }}
                    </a>
                </div>
            </div>
            
            <!-- Collapsed Ticket Icon -->
            <a
                x-show="!sidebarOpen"
                href="{{ route('tickets.index') }}"
                class="group relative flex items-center justify-center rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200 {{ $isTickets ? 'text-white bg-white/10 shadow-sm ring-1 ring-white/5' : 'text-white/60 hover:bg-white/5 hover:text-white' }}"
            >
                @if($isTickets)
                    <div class="absolute left-0 h-6 w-1 rounded-r-full bg-[var(--accent)] shadow-[0_0_10px_var(--accent)]"></div>
                @endif
                <iconify-icon icon="solar:ticket-bold-duotone" width="20" class="{{ $isTickets ? 'text-[var(--accent-soft)]' : 'text-white/50 group-hover:text-white/80' }} transition-colors"></iconify-icon>
                <div class="absolute left-full ml-2 hidden rounded-md bg-slate-900 px-2 py-1 text-xs text-white opacity-0 group-hover:block group-hover:opacity-100 z-50 whitespace-nowrap shadow-xl">
                    Tickets
                </div>
            </a>
        </div>

        <!-- Discussions -->
        <a
            href="{{ route('discussions.index') }}"
            class="group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200 {{ $isDiscussions ? 'text-white bg-white/10 shadow-sm ring-1 ring-white/5' : 'text-white/60 hover:bg-white/5 hover:text-white' }}"
            :class="sidebarOpen ? '' : 'justify-center'"
        >
            @if($isDiscussions)
                <div class="absolute left-0 h-6 w-1 rounded-r-full bg-[var(--accent)] shadow-[0_0_10px_var(--accent)]" x-show="sidebarOpen"></div>
            @endif
            <iconify-icon icon="solar:chat-round-bold-duotone" width="20" class="{{ $isDiscussions ? 'text-[var(--accent-soft)]' : 'text-white/50 group-hover:text-white/80' }} transition-colors"></iconify-icon>
            <span x-show="sidebarOpen" class="truncate">Discussions</span>
            
            <div x-show="!sidebarOpen" class="absolute left-full ml-2 hidden rounded-md bg-slate-900 px-2 py-1 text-xs text-white opacity-0 group-hover:block group-hover:opacity-100 z-50 whitespace-nowrap shadow-xl">
                Discussions
            </div>
        </a>

        <!-- Formulaires -->
        <a
            href="{{ route('forms.index') }}"
            class="group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200 {{ $isForms ? 'text-white bg-white/10 shadow-sm ring-1 ring-white/5' : 'text-white/60 hover:bg-white/5 hover:text-white' }}"
            :class="sidebarOpen ? '' : 'justify-center'"
        >
            @if($isForms)
                <div class="absolute left-0 h-6 w-1 rounded-r-full bg-[var(--accent)] shadow-[0_0_10px_var(--accent)]" x-show="sidebarOpen"></div>
            @endif
            <iconify-icon icon="solar:clipboard-text-bold-duotone" width="20" class="{{ $isForms ? 'text-[var(--accent-soft)]' : 'text-white/50 group-hover:text-white/80' }} transition-colors"></iconify-icon>
            <span x-show="sidebarOpen" class="truncate">Formulaires</span>

            <div x-show="!sidebarOpen" class="absolute left-full ml-2 hidden rounded-md bg-slate-900 px-2 py-1 text-xs text-white opacity-0 group-hover:block group-hover:opacity-100 z-50 whitespace-nowrap shadow-xl">
                Formulaires
            </div>
        </a>

        @if ($isStaff)
            <div class="px-3 mb-2 mt-6 text-[10px] font-bold uppercase tracking-widest text-white/30 transition-opacity duration-300" x-show="sidebarOpen">
                Administration
            </div>

            @php
                $isAdminUsers = request()->routeIs('admin.users');
                $isReports = request()->routeIs('reports.*');
                $isAdminForms = request()->routeIs('admin.forms*');
                $isSettings = request()->routeIs('admin.settings');
            @endphp

            <!-- Team -->
            <a
                href="{{ route('admin.users') }}"
                class="group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200 {{ $isAdminUsers ? 'text-white bg-white/10 shadow-sm ring-1 ring-white/5' : 'text-white/60 hover:bg-white/5 hover:text-white' }}"
                :class="sidebarOpen ? '' : 'justify-center'"
            >
                @if($isAdminUsers)
                    <div class="absolute left-0 h-6 w-1 rounded-r-full bg-[var(--accent)] shadow-[0_0_10px_var(--accent)]" x-show="sidebarOpen"></div>
                @endif
                <iconify-icon icon="solar:users-group-rounded-bold-duotone" width="20" class="{{ $isAdminUsers ? 'text-[var(--accent-soft)]' : 'text-white/50 group-hover:text-white/80' }} transition-colors"></iconify-icon>
                <span x-show="sidebarOpen" class="truncate">Équipe</span>
            </a>

            <!-- Reports -->
            <a
                href="{{ route('reports.index') }}"
                class="group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200 {{ $isReports ? 'text-white bg-white/10 shadow-sm ring-1 ring-white/5' : 'text-white/60 hover:bg-white/5 hover:text-white' }}"
                :class="sidebarOpen ? '' : 'justify-center'"
            >
                @if($isReports)
                    <div class="absolute left-0 h-6 w-1 rounded-r-full bg-[var(--accent)] shadow-[0_0_10px_var(--accent)]" x-show="sidebarOpen"></div>
                @endif
                <iconify-icon icon="solar:chart-2-bold-duotone" width="20" class="{{ $isReports ? 'text-[var(--accent-soft)]' : 'text-white/50 group-hover:text-white/80' }} transition-colors"></iconify-icon>
                <span x-show="sidebarOpen" class="truncate">Rapports</span>
            </a>

            <!-- Admin Formulaires -->
            <a
                href="{{ route('admin.forms') }}"
                class="group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200 {{ $isAdminForms ? 'text-white bg-white/10 shadow-sm ring-1 ring-white/5' : 'text-white/60 hover:bg-white/5 hover:text-white' }}"
                :class="sidebarOpen ? '' : 'justify-center'"
            >
                @if($isAdminForms)
                    <div class="absolute left-0 h-6 w-1 rounded-r-full bg-[var(--accent)] shadow-[0_0_10px_var(--accent)]" x-show="sidebarOpen"></div>
                @endif
                <iconify-icon icon="solar:document-add-bold-duotone" width="20" class="{{ $isAdminForms ? 'text-[var(--accent-soft)]' : 'text-white/50 group-hover:text-white/80' }} transition-colors"></iconify-icon>
                <span x-show="sidebarOpen" class="truncate">Formulaires</span>
            </a>

            <!-- Settings -->
            <a
                href="{{ route('admin.settings') }}"
                class="group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200 {{ $isSettings ? 'text-white bg-white/10 shadow-sm ring-1 ring-white/5' : 'text-white/60 hover:bg-white/5 hover:text-white' }}"
                :class="sidebarOpen ? '' : 'justify-center'"
            >
                @if($isSettings)
                    <div class="absolute left-0 h-6 w-1 rounded-r-full bg-[var(--accent)] shadow-[0_0_10px_var(--accent)]" x-show="sidebarOpen"></div>
                @endif
                <iconify-icon icon="solar:settings-bold-duotone" width="20" class="{{ $isSettings ? 'text-[var(--accent-soft)]' : 'text-white/50 group-hover:text-white/80' }} transition-colors"></iconify-icon>
                <span x-show="sidebarOpen" class="truncate">Paramètres</span>
            </a>
        @endif
    </div>

    <!-- Sidebar Footer -->
    <div class="border-t border-white/5 px-4 py-4" x-show="sidebarOpen">
        <div class="rounded-xl bg-white/5 p-3 ring-1 ring-white/5">
            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/10 text-xs font-bold text-white">
                    {{ substr($org?->name ?? 'M', 0, 1) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-xs font-medium text-white">{{ $org?->name ?? 'Entreprise' }}</p>
                    <p class="truncate text-[10px] text-white/40">Plan Gratuit</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Collapse Button (Desktop) -->
    <button 
        type="button" 
        class="absolute -right-3 top-20 hidden md:flex h-6 w-6 items-center justify-center rounded-full bg-white text-slate-400 shadow-md ring-1 ring-slate-100 hover:text-[var(--accent)] transition-colors z-50" 
        @click="sidebarOpen = !sidebarOpen"
    >
        <iconify-icon :icon="sidebarOpen ? 'solar:alt-arrow-left-linear' : 'solar:alt-arrow-right-linear'" width="14"></iconify-icon>
    </button>
</aside>