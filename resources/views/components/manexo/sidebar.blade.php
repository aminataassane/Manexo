@php
    // currentOrganization is injected by EnsureOrganizationIsSelected middleware (includes pivot role).
    $org = $currentOrganization ?? request()->attributes->get('currentOrganization');
    $orgRole = (string) ($org?->pivot?->role ?? 'member');
    /** @var \App\Models\User|null $authUser */
    $authUser = \Illuminate\Support\Facades\Auth::user();
    // Admin section visible if user has at least one admin-level permission.
    $canSeeTeam = $authUser && $authUser->hasAnyPermission([
        \App\Enums\Permission::TeamInvite,
        \App\Enums\Permission::TeamEditRole,
        \App\Enums\Permission::TeamRemove,
    ]);
    $canSeeReports = $authUser && $authUser->hasPermission(\App\Enums\Permission::ReportsView);
    $canSeeForms = $authUser && $authUser->hasPermission(\App\Enums\Permission::SettingsManageForms);
    $canSeeSettings = $authUser && $authUser->hasAnyPermission([
        \App\Enums\Permission::SettingsManageBranding,
        \App\Enums\Permission::SettingsManageCategories,
        \App\Enums\Permission::SettingsManagePriorities,
        \App\Enums\Permission::SettingsManageFunctions,
        \App\Enums\Permission::SettingsManageRoles,
        \App\Enums\Permission::SettingsDeleteOrg,
    ]);
    $isStaff = $canSeeTeam || $canSeeReports || $canSeeForms || $canSeeSettings;
    // Badges sidebar : mêmes clés que CacheHelper (invalidées par la cloche / temps réel), filtrées par org courante.
    $sidebarUserId = (int) ($authUser?->id ?? 0);
    $sidebarOrgId = (int) ($org?->id ?? session('current_organization_id', 0));
    $discussionsUnreadCount = \Illuminate\Support\Facades\Cache::remember(
        \App\Helpers\CacheHelper::sidebarDiscussionsUnreadKey($sidebarUserId, $sidebarOrgId),
        \App\Helpers\CacheHelper::TTL_SHORT,
        function () use ($authUser, $sidebarOrgId) {
            if (! $authUser) {
                return 0;
            }
            $q = $authUser->unreadNotifications()
                ->whereIn('type', [
                    \App\Notifications\DiscussionNewMessageNotification::class,
                    \App\Notifications\DiscussionInviteNotification::class,
                ]);
            \App\Support\NotificationOrganizationScope::apply($q, $sidebarOrgId);

            return (int) $q->count();
        }
    );
    $notificationsUnreadCount = \Illuminate\Support\Facades\Cache::remember(
        \App\Helpers\CacheHelper::notificationsUnreadCountKey($sidebarUserId, $sidebarOrgId),
        \App\Helpers\CacheHelper::TTL_SHORT,
        function () use ($authUser, $sidebarOrgId) {
            if (! $authUser) {
                return 0;
            }
            $q = $authUser->unreadNotifications();
            \App\Support\NotificationOrganizationScope::apply($q, $sidebarOrgId);

            return (int) $q->count();
        }
    );
    // Logo: organisation (URL relative à la requête pour éviter erreur de chargement)
    $logoUrl = $org && $org->logo_path ? asset('storage/' . ltrim($org->logo_path, '/')) : null;
    $brandName = $org?->name ?? 'Manexo';
    // Créateur de la plateforme (empreinte visible pour tous les connectés)
    $platformName = config('app.platform_creator.name', 'Manexo');
    $platformLogo = config('app.platform_creator.logo');
    $platformLogoUrl = null;
    if ($platformLogo) {
        if (str_starts_with($platformLogo, 'http')) {
            $platformLogoUrl = $platformLogo;
        } else {
            $logoPath = ltrim($platformLogo, '/');
            if (\Illuminate\Support\Facades\File::exists(public_path($logoPath))) {
                $platformLogoUrl = asset($logoPath);
            } else {
                // Fallback : essayer assets/Logo(1).png si le fichier configuré est introuvable
                $fallback = 'assets/Logo(1).png';
                if (\Illuminate\Support\Facades\File::exists(public_path($fallback))) {
                    $platformLogoUrl = asset($fallback);
                }
            }
        }
    }
    $platformUrl = config('app.platform_creator.url');
@endphp

<aside
    class="manexo-shell-transition fixed left-0 top-0 z-40 flex h-screen shrink-0 flex-col border-r border-white/5 text-white/60 transition-[width] duration-300 ease-[cubic-bezier(0.25,1,0.5,1)] max-w-[85vw] md:max-w-none backdrop-blur-xl"
    style="background: linear-gradient(180deg, var(--accent-dark) 0%, color-mix(in srgb, var(--accent-dark) 90%, black) 100%);"
    :class="{
        'w-[min(85vw,var(--manexo-sidebar-expanded))]': sidebarOpen,
        'w-[var(--manexo-sidebar-collapsed)]': !sidebarOpen,
        '-translate-x-full md:translate-x-0': !mobileOpen,
        'translate-x-0': mobileOpen
    }"
>
    <!-- LOGO AREA : logo entreprise (repli sur initiale si image ne charge pas) -->
    <div class="flex h-16 shrink-0 items-center px-4 xl:px-5" :class="sidebarOpen ? 'justify-start' : 'justify-center'">
        <a href="{{ route('dashboard') }}" wire:navigate.hover class="flex items-center gap-3 group transition-all duration-300">
            <span class="relative h-9 w-9 shrink-0 rounded-xl overflow-hidden ring-1 ring-white/10 shadow-lg shadow-[var(--accent-ring)]">
                {{-- Fallback : initiale (affiché si pas de logo ou si image en erreur) --}}
                <span id="sidebar-org-logo-fallback"
                      class="absolute inset-0 flex items-center justify-center font-bold text-sm transition-transform group-hover:scale-105"
                      style="background: linear-gradient(135deg, var(--accent-soft), var(--accent)); color: var(--accent-dark); {{ $logoUrl ? 'display: none' : '' }}">
                    {{ mb_substr($brandName, 0, 1) }}
                </span>
                @if ($logoUrl)
                    <img src="{{ $logoUrl }}"
                         alt="{{ $brandName }}"
                         class="h-full w-full object-contain object-center absolute inset-0 group-hover:scale-105 transition-transform"
                         loading="eager"
                         onerror="this.style.display='none'; document.getElementById('sidebar-org-logo-fallback').style.display='flex';"
                    />
                @endif
            </span>
            <div class="flex flex-col min-w-0" x-show="sidebarOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0">
                <span class="text-sm font-bold text-white tracking-wide truncate">{{ strtoupper($brandName) }}</span>
                <span class="text-[10px] font-medium text-white/40 uppercase tracking-widest">Helpdesk</span>
            </div>
        </a>
    </div>

    <!-- Navigation -->
    <div class="custom-scrollbar flex flex-1 flex-col gap-1 overflow-y-auto px-3 py-4">

        <!-- Section Label -->
        <div class="px-3 mb-2 mt-2 text-[10px] font-bold uppercase tracking-widest text-white/30 transition-opacity duration-300" x-show="sidebarOpen">
            {{ __('menu.main_menu') }}
        </div>

        <!-- Navigation Links (reactive to URL changes via wire:navigate) -->
        <div
            x-data="{
                path: window.location.pathname,
                search: window.location.search,
                ticketsOpen: false,
                notifUnread: {{ (int) $notificationsUnreadCount }},
                discUnread: {{ (int) $discussionsUnreadCount }},
                init() {
                    this.update();
                    document.addEventListener('livewire:navigated', () => this.update());
                    window.addEventListener('manexo-sidebar-badges', (e) => {
                        const d = e.detail || {};
                        if (typeof d.notifications === 'number') this.notifUnread = d.notifications;
                        if (typeof d.discussions === 'number') this.discUnread = d.discussions;
                    });
                },
                update() {
                    this.path = window.location.pathname;
                    this.search = window.location.search;
                    if (this.isTickets) this.ticketsOpen = true;
                },
                get isDashboard() { return this.path === '/' || this.path === '/dashboard'; },
                get isTickets() { return this.path.startsWith('/tickets') || this.path.startsWith('/conversation'); },
                get isTicketsIndex() { return this.path === '/tickets' || this.path === '/tickets/'; },
                get isTicketsAll() { return this.isTicketsIndex; },
                get isTicketsGroups() { return this.path.startsWith('/tickets/groups'); },
                get isDiscussions() { return this.path.startsWith('/discussions'); },
                get isForms() { return this.path.startsWith('/forms'); },
                get isKnowledgeBase() { return this.path.startsWith('/knowledge-base'); },
                get isMyTasks() { return this.path === '/reports/tasks'; },
                get isNotifications() { return this.path.startsWith('/notifications'); },
            }"
            class="contents"
        >

        <!-- Dashboard -->
        <a
            href="{{ route('dashboard') }}"
            wire:navigate.hover
            class="group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200"
            :class="[
                isDashboard ? 'text-white bg-white/10 shadow-sm ring-1 ring-white/5' : 'text-white/60 hover:bg-white/5 hover:text-white',
                sidebarOpen ? '' : 'justify-center'
            ]"
        >
            <div x-show="isDashboard && sidebarOpen" class="absolute left-0 h-6 w-1 rounded-r-full bg-[var(--accent)] shadow-[0_0_10px_var(--accent)]"></div>
            <iconify-icon icon="solar:widget-5-bold-duotone" width="20" :class="isDashboard ? 'text-[var(--accent-soft)]' : 'text-white/50 group-hover:text-white/80'" class="transition-colors"></iconify-icon>
            <span x-show="sidebarOpen" class="truncate">{{ __('menu.dashboard') }}</span>

            <!-- Tooltip for collapsed state -->
            <div x-show="!sidebarOpen" class="absolute left-full ml-2 hidden rounded-md bg-slate-900 px-2 py-1 text-xs text-white opacity-0 group-hover:block group-hover:opacity-100 z-50 whitespace-nowrap shadow-xl">
                {{ __('menu.dashboard') }}
            </div>
        </a>

        <!-- Tickets -->
        <div>
            <div x-show="sidebarOpen">
                <button
                    type="button"
                    @click="ticketsOpen = !ticketsOpen"
                    class="group flex w-full items-center justify-between rounded-xl px-3 py-2.5 text-left text-sm font-medium transition-all duration-200"
                    :class="isTickets ? 'text-white' : 'text-white/60 hover:bg-white/5 hover:text-white'"
                >
                    <div class="flex items-center gap-3">
                        <iconify-icon icon="solar:ticket-bold-duotone" width="20" :class="isTickets ? 'text-[var(--accent-soft)]' : 'text-white/50 group-hover:text-white/80'" class="transition-colors"></iconify-icon>
                        <span>{{ __('menu.tickets') }}</span>
                    </div>
                    <iconify-icon :icon="ticketsOpen ? 'solar:alt-arrow-up-linear' : 'solar:alt-arrow-down-linear'" width="12" class="opacity-50 transition-transform duration-200" :class="ticketsOpen ? 'rotate-0' : '-rotate-90'"></iconify-icon>
                </button>

                <div x-show="ticketsOpen" x-collapse class="mt-1 space-y-1 px-3">
                    <a
                        href="{{ route('tickets.index') }}"
                        wire:navigate.hover
                        class="flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-medium transition-colors"
                        :class="isTicketsAll ? 'bg-white/10 text-[var(--accent-soft)]' : 'text-white/40 hover:text-white hover:bg-white/5'"
                    >
                        <div class="h-1.5 w-1.5 rounded-full" :class="isTicketsAll ? 'bg-[var(--accent)]' : 'bg-white/20'"></div>
                        {{ __('pages.tickets.all_tickets') }}
                    </a>
                    <a
                        href="{{ route('tickets.groups') }}"
                        wire:navigate.hover
                        class="flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-medium transition-colors"
                        :class="isTicketsGroups ? 'bg-white/10 text-[var(--accent-soft)]' : 'text-white/40 hover:text-white hover:bg-white/5'"
                    >
                        <div class="h-1.5 w-1.5 rounded-full" :class="isTicketsGroups ? 'bg-[var(--accent)]' : 'bg-white/20'"></div>
                        {{ __('menu.groups_view') }}
                    </a>
                </div>
            </div>

            <!-- Collapsed Ticket Icon -->
            <a
                x-show="!sidebarOpen"
                href="{{ route('tickets.index') }}"
                wire:navigate.hover
                class="group relative flex items-center justify-center rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200"
                :class="isTickets ? 'text-white bg-white/10 shadow-sm ring-1 ring-white/5' : 'text-white/60 hover:bg-white/5 hover:text-white'"
            >
                <div x-show="isTickets" class="absolute left-0 h-6 w-1 rounded-r-full bg-[var(--accent)] shadow-[0_0_10px_var(--accent)]"></div>
                <iconify-icon icon="solar:ticket-bold-duotone" width="20" :class="isTickets ? 'text-[var(--accent-soft)]' : 'text-white/50 group-hover:text-white/80'" class="transition-colors"></iconify-icon>
                <div class="absolute left-full ml-2 hidden rounded-md bg-slate-900 px-2 py-1 text-xs text-white opacity-0 group-hover:block group-hover:opacity-100 z-50 whitespace-nowrap shadow-xl">
                    {{ __('menu.tickets') }}
                </div>
            </a>
        </div>

        <!-- Discussions -->
        <a
            href="{{ route('discussions.index') }}"
            wire:navigate.hover
            class="group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200"
            :class="[
                isDiscussions ? 'text-white bg-white/10 shadow-sm ring-1 ring-white/5' : 'text-white/60 hover:bg-white/5 hover:text-white',
                sidebarOpen ? '' : 'justify-center'
            ]"
        >
            <div x-show="isDiscussions && sidebarOpen" class="absolute left-0 h-6 w-1 rounded-r-full bg-[var(--accent)] shadow-[0_0_10px_var(--accent)]"></div>
            <span class="relative shrink-0">
                <iconify-icon icon="solar:inbox-bold-duotone" width="20" :class="isDiscussions ? 'text-[var(--accent-soft)]' : 'text-white/50 group-hover:text-white/80'" class="transition-colors"></iconify-icon>
                <span x-show="discUnread > 0 && !sidebarOpen" class="absolute -top-1 -right-1 flex h-4 min-w-[16px] items-center justify-center rounded-full bg-red-500 px-1 text-[9px] font-bold text-white ring-2 ring-[color:var(--accent-dark)]" x-cloak x-text="discUnread > 99 ? '99+' : discUnread"></span>
            </span>
            <span x-show="sidebarOpen" class="truncate">{{ __('menu.discussions') }}</span>
            <span x-show="discUnread > 0 && sidebarOpen" class="ml-auto flex h-5 min-w-[20px] items-center justify-center rounded-full bg-red-500 px-1.5 text-[10px] font-bold text-white ring-2 ring-white/20" x-text="discUnread > 99 ? '99+' : discUnread"></span>

            <div x-show="!sidebarOpen" class="absolute left-full ml-2 hidden rounded-md bg-slate-900 px-2 py-1 text-xs text-white opacity-0 group-hover:block group-hover:opacity-100 z-50 whitespace-nowrap shadow-xl">
                {{ __('menu.discussions') }}
            </div>
        </a>

        <!-- Formulaires -->
        <a
            href="{{ route('forms.index') }}"
            wire:navigate.hover
            class="group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200"
            :class="[
                isForms ? 'text-white bg-white/10 shadow-sm ring-1 ring-white/5' : 'text-white/60 hover:bg-white/5 hover:text-white',
                sidebarOpen ? '' : 'justify-center'
            ]"
        >
            <div x-show="isForms && sidebarOpen" class="absolute left-0 h-6 w-1 rounded-r-full bg-[var(--accent)] shadow-[0_0_10px_var(--accent)]"></div>
            <iconify-icon icon="solar:clipboard-text-bold-duotone" width="20" :class="isForms ? 'text-[var(--accent-soft)]' : 'text-white/50 group-hover:text-white/80'" class="transition-colors"></iconify-icon>
            <span x-show="sidebarOpen" class="truncate">{{ __('menu.forms') }}</span>

            <div x-show="!sidebarOpen" class="absolute left-full ml-2 hidden rounded-md bg-slate-900 px-2 py-1 text-xs text-white opacity-0 group-hover:block group-hover:opacity-100 z-50 whitespace-nowrap shadow-xl">
                {{ __('menu.forms') }}
            </div>
        </a>

        <!-- {{ __('menu.knowledge_base') }} -->
        <a
            href="{{ route('knowledge-base.index') }}"
            wire:navigate.hover
            class="group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200"
            :class="[
                isKnowledgeBase ? 'text-white bg-white/10 shadow-sm ring-1 ring-white/5' : 'text-white/60 hover:bg-white/5 hover:text-white',
                sidebarOpen ? '' : 'justify-center'
            ]"
        >
            <div x-show="isKnowledgeBase && sidebarOpen" class="absolute left-0 h-6 w-1 rounded-r-full bg-[var(--accent)] shadow-[0_0_10px_var(--accent)]"></div>
            <iconify-icon icon="solar:book-2-bold-duotone" width="20" :class="isKnowledgeBase ? 'text-[var(--accent-soft)]' : 'text-white/50 group-hover:text-white/80'" class="transition-colors"></iconify-icon>
            <span x-show="sidebarOpen" class="truncate">{{ __('menu.knowledge_base') }}</span>

            <div x-show="!sidebarOpen" class="absolute left-full ml-2 hidden rounded-md bg-slate-900 px-2 py-1 text-xs text-white opacity-0 group-hover:block group-hover:opacity-100 z-50 whitespace-nowrap shadow-xl">
                {{ __('menu.knowledge_base') }}
            </div>
        </a>

        <!-- Mes tâches (visible si permission view_tasks mais pas la vue globale reports) -->
        @if(!$canSeeReports && $authUser && $authUser->hasPermission(\App\Enums\Permission::ReportsViewTasks))
        <a
            href="{{ route('reports.tasks') }}"
            wire:navigate.hover
            class="group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200"
            :class="[
                isMyTasks ? 'text-white bg-white/10 shadow-sm ring-1 ring-white/5' : 'text-white/60 hover:bg-white/5 hover:text-white',
                sidebarOpen ? '' : 'justify-center'
            ]"
        >
            <div x-show="isMyTasks && sidebarOpen" class="absolute left-0 h-6 w-1 rounded-r-full bg-[var(--accent)] shadow-[0_0_10px_var(--accent)]"></div>
            <iconify-icon icon="solar:checklist-bold-duotone" width="20" :class="isMyTasks ? 'text-[var(--accent-soft)]' : 'text-white/50 group-hover:text-white/80'" class="transition-colors"></iconify-icon>
            <span x-show="sidebarOpen" class="truncate">{{ __('menu.reports_tasks') }}</span>
            <div x-show="!sidebarOpen" class="absolute left-full ml-2 hidden rounded-md bg-slate-900 px-2 py-1 text-xs text-white opacity-0 group-hover:block group-hover:opacity-100 z-50 whitespace-nowrap shadow-xl">
                {{ __('menu.reports_tasks') }}
            </div>
        </a>
        @endif

        <!-- Notifications -->
        <a
            href="{{ route('notifications.index') }}"
            wire:navigate.hover
            class="group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200"
            :class="[
                isNotifications ? 'text-white bg-white/10 shadow-sm ring-1 ring-white/5' : 'text-white/60 hover:bg-white/5 hover:text-white',
                sidebarOpen ? '' : 'justify-center'
            ]"
        >
            <div x-show="isNotifications && sidebarOpen" class="absolute left-0 h-6 w-1 rounded-r-full bg-[var(--accent)] shadow-[0_0_10px_var(--accent)]"></div>
            <span class="relative shrink-0">
                <iconify-icon icon="solar:bell-bold-duotone" width="20" :class="isNotifications ? 'text-[var(--accent-soft)]' : 'text-white/50 group-hover:text-white/80'" class="transition-colors"></iconify-icon>
                <span x-show="notifUnread > 0 && !sidebarOpen" class="absolute -top-1 -right-1 flex h-4 min-w-[16px] items-center justify-center rounded-full bg-red-500 px-1 text-[9px] font-bold text-white ring-2 ring-[color:var(--accent-dark)]" x-cloak x-text="notifUnread > 99 ? '99+' : notifUnread"></span>
            </span>
            <span x-show="sidebarOpen" class="truncate">{{ __('menu.notifications') }}</span>
            <span x-show="notifUnread > 0 && sidebarOpen" class="ml-auto flex h-5 min-w-[20px] items-center justify-center rounded-full bg-red-500 px-1.5 text-[10px] font-bold text-white ring-2 ring-white/20" x-text="notifUnread > 99 ? '99+' : notifUnread"></span>

            <div x-show="!sidebarOpen" class="absolute left-full ml-2 hidden rounded-md bg-slate-900 px-2 py-1 text-xs text-white opacity-0 group-hover:block group-hover:opacity-100 z-50 whitespace-nowrap shadow-xl">
                {{ __('menu.notifications') }}
            </div>
        </a>

        </div><!-- end x-data nav-state -->

        @if ($isStaff)
            <div class="px-3 mb-2 mt-6 text-[10px] font-bold uppercase tracking-widest text-white/30 transition-opacity duration-300" x-show="sidebarOpen">
                {{ __('menu.administration') }}
            </div>

            <div
                x-data="{
                    aPath: window.location.pathname,
                    reportsOpen: false,
                    init() {
                        this.updateAdmin();
                        document.addEventListener('livewire:navigated', () => this.updateAdmin());
                    },
                    updateAdmin() {
                        this.aPath = window.location.pathname;
                        if (this.isReports) this.reportsOpen = true;
                    },
                    get isAdminUsers() { return this.aPath === '/admin/users'; },
                    get isReportsOverview() { return this.aPath === '/reports' || this.aPath === '/reports/'; },
                    get isReportsTasks() { return this.aPath === '/reports/tasks'; },
                    get isReportsDaily() { return this.aPath === '/reports/daily'; },
                    get isReports() { return this.isReportsOverview || this.isReportsTasks || this.isReportsDaily; },
                    get isAdminForms() { return this.aPath.startsWith('/admin/forms'); },
                    get isSettings() { return this.aPath === '/admin/settings'; },
                }"
                class="contents"
            >

            @if ($canSeeTeam)
            <!-- Team -->
            <a
                href="{{ route('admin.users') }}"
                wire:navigate.hover
                class="group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200"
                :class="[
                    isAdminUsers ? 'text-white bg-white/10 shadow-sm ring-1 ring-white/5' : 'text-white/60 hover:bg-white/5 hover:text-white',
                    sidebarOpen ? '' : 'justify-center'
                ]"
            >
                <div x-show="isAdminUsers && sidebarOpen" class="absolute left-0 h-6 w-1 rounded-r-full bg-[var(--accent)] shadow-[0_0_10px_var(--accent)]"></div>
                <iconify-icon icon="solar:users-group-rounded-bold-duotone" width="20" :class="isAdminUsers ? 'text-[var(--accent-soft)]' : 'text-white/50 group-hover:text-white/80'" class="transition-colors"></iconify-icon>
                <span x-show="sidebarOpen" class="truncate">{{ __('menu.team') }}</span>
                <div x-show="!sidebarOpen" class="absolute left-full ml-2 hidden rounded-md bg-slate-900 px-2 py-1 text-xs text-white opacity-0 group-hover:block group-hover:opacity-100 z-50 whitespace-nowrap shadow-xl">
                    {{ __('menu.team') }}
                </div>
            </a>
            @endif

            @if ($canSeeReports)
            <!-- Reports (submenu) -->
            <div>
                <div x-show="sidebarOpen">
                    <button
                        type="button"
                        @click="reportsOpen = !reportsOpen"
                        class="group flex w-full items-center justify-between rounded-xl px-3 py-2.5 text-left text-sm font-medium transition-all duration-200"
                        :class="isReports ? 'text-white' : 'text-white/60 hover:bg-white/5 hover:text-white'"
                    >
                        <div class="flex items-center gap-3">
                            <iconify-icon icon="solar:chart-2-bold-duotone" width="20" :class="isReports ? 'text-[var(--accent-soft)]' : 'text-white/50 group-hover:text-white/80'" class="transition-colors"></iconify-icon>
                            <span>{{ __('menu.reports') }}</span>
                        </div>
                        <iconify-icon :icon="reportsOpen ? 'solar:alt-arrow-up-linear' : 'solar:alt-arrow-down-linear'" width="12" class="opacity-50 transition-transform duration-200" :class="reportsOpen ? 'rotate-0' : '-rotate-90'"></iconify-icon>
                    </button>

                    <div x-show="reportsOpen" x-collapse class="mt-1 space-y-1 px-3">
                        <a
                            href="{{ route('reports.index') }}"
                            wire:navigate.hover
                            class="flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-medium transition-colors"
                            :class="isReportsOverview ? 'bg-white/10 text-[var(--accent-soft)]' : 'text-white/40 hover:text-white hover:bg-white/5'"
                        >
                            <div class="h-1.5 w-1.5 rounded-full" :class="isReportsOverview ? 'bg-[var(--accent)]' : 'bg-white/20'"></div>
                            {{ __('menu.reports_overview') }}
                        </a>
                        <a
                            href="{{ route('reports.tasks') }}"
                            wire:navigate.hover
                            class="flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-medium transition-colors"
                            :class="isReportsTasks ? 'bg-white/10 text-[var(--accent-soft)]' : 'text-white/40 hover:text-white hover:bg-white/5'"
                        >
                            <div class="h-1.5 w-1.5 rounded-full" :class="isReportsTasks ? 'bg-[var(--accent)]' : 'bg-white/20'"></div>
                            {{ __('menu.reports_tasks') }}
                        </a>
                        <a
                            href="{{ route('reports.daily') }}"
                            wire:navigate.hover
                            class="flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-medium transition-colors"
                            :class="isReportsDaily ? 'bg-white/10 text-[var(--accent-soft)]' : 'text-white/40 hover:text-white hover:bg-white/5'"
                        >
                            <div class="h-1.5 w-1.5 rounded-full" :class="isReportsDaily ? 'bg-[var(--accent)]' : 'bg-white/20'"></div>
                            {{ __('menu.reports_daily') }}
                        </a>
                    </div>
                </div>

                <!-- Collapsed Reports Icon -->
                <a
                    x-show="!sidebarOpen"
                    href="{{ route('reports.index') }}"
                    wire:navigate.hover
                    class="group relative flex items-center justify-center rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200"
                    :class="isReports ? 'text-white bg-white/10 shadow-sm ring-1 ring-white/5' : 'text-white/60 hover:bg-white/5 hover:text-white'"
                >
                    <div x-show="isReports" class="absolute left-0 h-6 w-1 rounded-r-full bg-[var(--accent)] shadow-[0_0_10px_var(--accent)]"></div>
                    <iconify-icon icon="solar:chart-2-bold-duotone" width="20" :class="isReports ? 'text-[var(--accent-soft)]' : 'text-white/50 group-hover:text-white/80'" class="transition-colors"></iconify-icon>
                    <div class="absolute left-full ml-2 hidden rounded-md bg-slate-900 px-2 py-1 text-xs text-white opacity-0 group-hover:block group-hover:opacity-100 z-50 whitespace-nowrap shadow-xl">
                        {{ __('menu.reports') }}
                    </div>
                </a>
            </div>
            @endif

            @if ($canSeeForms)
            <!-- Admin Formulaires -->
            <a
                href="{{ route('admin.forms') }}"
                wire:navigate.hover
                class="group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200"
                :class="[
                    isAdminForms ? 'text-white bg-white/10 shadow-sm ring-1 ring-white/5' : 'text-white/60 hover:bg-white/5 hover:text-white',
                    sidebarOpen ? '' : 'justify-center'
                ]"
            >
                <div x-show="isAdminForms && sidebarOpen" class="absolute left-0 h-6 w-1 rounded-r-full bg-[var(--accent)] shadow-[0_0_10px_var(--accent)]"></div>
                <iconify-icon icon="solar:document-add-bold-duotone" width="20" :class="isAdminForms ? 'text-[var(--accent-soft)]' : 'text-white/50 group-hover:text-white/80'" class="transition-colors"></iconify-icon>
                <span x-show="sidebarOpen" class="truncate">{{ __('menu.forms') }}</span>
            </a>
            @endif

            @if ($canSeeSettings)
            <!-- Settings -->
            <a
                href="{{ route('admin.settings') }}"
                wire:navigate.hover
                class="group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200"
                :class="[
                    isSettings ? 'text-white bg-white/10 shadow-sm ring-1 ring-white/5' : 'text-white/60 hover:bg-white/5 hover:text-white',
                    sidebarOpen ? '' : 'justify-center'
                ]"
            >
                <div x-show="isSettings && sidebarOpen" class="absolute left-0 h-6 w-1 rounded-r-full bg-[var(--accent)] shadow-[0_0_10px_var(--accent)]"></div>
                <iconify-icon icon="solar:settings-bold-duotone" width="20" :class="isSettings ? 'text-[var(--accent-soft)]' : 'text-white/50 group-hover:text-white/80'" class="transition-colors"></iconify-icon>
                <span x-show="sidebarOpen" class="truncate">{{ __('menu.settings') }}</span>
            </a>
            @endif

            </div><!-- end admin x-data -->
        @endif

    </div>

    <!-- Sidebar Footer (ordre : plateforme → espace → langue) -->
    <div class="border-t border-white/5 px-4 py-4 space-y-2" x-show="sidebarOpen">
        {{-- 1. Empreinte créateur de la plateforme (en premier) — fond clair pour logo sombre --}}
        <a href="{{ $platformUrl }}" target="_blank" rel="noopener noreferrer" class="group flex items-center gap-2.5 rounded-xl bg-white/5 px-3 py-2.5 ring-1 ring-white/5 hover:bg-white/10 hover:ring-white/10 transition-all duration-200">
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white shadow-sm ring-1 ring-black/5 p-1">
                @if ($platformLogoUrl)
                    <img src="{{ $platformLogoUrl }}" alt="{{ $platformName }}" class="h-full w-full object-contain" />
                @else
                    <span class="text-xs font-bold text-slate-700">{{ mb_substr($platformName, 0, 1) }}</span>
                @endif
            </span>
            <div class="min-w-0 flex-1">
                <span class="text-[10px] font-medium uppercase tracking-wider text-white/40">{{ __('menu.powered_by') }}</span>
                <span class="block truncate text-xs font-semibold text-white/80 group-hover:text-white transition-colors">{{ $platformName }}</span>
            </div>
            <iconify-icon icon="solar:link-round-linear" class="h-3.5 w-3.5 shrink-0 text-white/30 group-hover:text-white/60 transition-colors" aria-hidden="true"></iconify-icon>
        </a>

        {{-- 2. Carte espace actuel (Quality Center, etc.) --}}
        <div class="rounded-xl bg-white/5 p-3 ring-1 ring-white/5">
            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/10 text-xs font-bold text-white">
                    {{ substr($org?->name ?? 'M', 0, 1) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-xs font-medium text-white">{{ $org?->name ?? __('menu.company') }}</p>
                    <p class="truncate text-[10px] text-white/40">{{ __('menu.free_plan') }}</p>
                </div>
            </div>
        </div>

        {{-- 3. Sélecteur de langue --}}
        <div class="flex items-center gap-1 rounded-lg bg-white/5 p-2 ring-1 ring-white/5">
            <span class="text-[10px] font-semibold uppercase tracking-wider text-white/40 shrink-0">{{ __('menu.language') }}</span>
            <div class="flex gap-1 flex-1">
                <a href="{{ route('locale.switch', 'fr') }}" class="flex-1 rounded-md px-2 py-1.5 text-center text-[11px] font-semibold transition-colors {{ app()->getLocale() === 'fr' ? 'bg-white/20 text-white' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">{{ __('menu.french') }}</a>
                <a href="{{ route('locale.switch', 'en') }}" class="flex-1 rounded-md px-2 py-1.5 text-center text-[11px] font-semibold transition-colors {{ app()->getLocale() === 'en' ? 'bg-white/20 text-white' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">{{ __('menu.english') }}</a>
            </div>
        </div>
    </div>

    {{-- Empreinte plateforme (sidebar repliée) : logo sur fond blanc pour lisibilité --}}
    <div class="border-t border-white/5 px-2 py-3 flex justify-center" x-show="!sidebarOpen" x-cloak>
        <a href="{{ $platformUrl }}" target="_blank" rel="noopener noreferrer" class="group flex h-10 w-10 items-center justify-center rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-1.5 hover:ring-white/20 transition-colors" title="{{ __('menu.powered_by') }} {{ $platformName }}">
            @if ($platformLogoUrl)
                <img src="{{ $platformLogoUrl }}" alt="{{ $platformName }}" class="h-full w-full object-contain" />
            @else
                <span class="text-xs font-bold text-slate-700">{{ mb_substr($platformName, 0, 1) }}</span>
            @endif
        </a>
    </div>

    <!-- Collapse Button (Desktop) -->
    <button
        type="button"
        class="absolute -right-3 top-20 hidden md:flex h-6 w-6 items-center justify-center rounded-full bg-white text-slate-400 shadow-md ring-1 ring-slate-100 hover:text-[var(--accent)] transition-colors z-50"
        @click="
            sidebarOpen = !sidebarOpen;
            const offset = sidebarOpen ? 'var(--manexo-sidebar-expanded)' : 'var(--manexo-sidebar-collapsed)';
            document.body.style.setProperty('--manexo-shell-offset', offset);
            localStorage.setItem('manexo_sidebar', sidebarOpen ? 'true' : 'false');
        "
    >
        <iconify-icon :icon="sidebarOpen ? 'solar:alt-arrow-left-linear' : 'solar:alt-arrow-right-linear'" width="14"></iconify-icon>
    </button>
</aside>
