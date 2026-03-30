@php
    $statusLabel = function (string $status): array {
        return match ($status) {
            'open' => [__('tickets.status.open'), 'solar:bolt-circle-bold-duotone'],
            'in_progress' => [__('tickets.status.in_progress'), 'solar:clock-circle-bold-duotone'],
            'pending' => [__('tickets.status.pending'), 'solar:hourglass-bold-duotone'],
            'resolved' => [__('tickets.status.resolved'), 'solar:check-circle-bold-duotone'],
            'closed' => [__('tickets.status.closed'), 'solar:lock-keyhole-bold-duotone'],
            default => [__('tickets.status.' . $status) ?: ucfirst(str_replace('_', ' ', $status)), 'solar:question-circle-bold-duotone'],
        };
    };

    $statusPill = function (string $status): array {
        return match ($status) {
            'open' => ['bg' => 'bg-red-50', 'text' => 'text-red-600', 'border' => 'border-red-100/80'],
            'in_progress' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'border' => 'border-blue-100/80'],
            'pending' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'border' => 'border-amber-100/80'],
            'resolved' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'border' => 'border-emerald-100/80'],
            'closed' => ['bg' => 'bg-slate-50', 'text' => 'text-slate-600', 'border' => 'border-slate-100/80'],
            default => ['bg' => 'bg-slate-50', 'text' => 'text-slate-600', 'border' => 'border-slate-100/80'],
        };
    };

    $priorityMeta = function (?int $level): array {
        if ($level === null) {
            return ['label' => '—', 'dot' => 'bg-slate-300', 'text' => 'text-slate-500'];
        }

        return match (true) {
            $level >= 4 => ['label' => __('Critique'), 'dot' => 'bg-red-500', 'text' => 'text-red-600'],
            $level === 3 => ['label' => __('Haute'), 'dot' => 'bg-amber-500', 'text' => 'text-amber-600'],
            $level === 2 => ['label' => __('Moyenne'), 'dot' => 'bg-blue-500', 'text' => 'text-blue-600'],
            default => ['label' => __('Basse'), 'dot' => 'bg-emerald-500', 'text' => 'text-emerald-600'],
        };
    };

    $boxKey = $box ?? 'active';
    $isActive = fn (string $k) => ($viewKey ?? 'all') === $k;
@endphp

<div
    class="w-full max-w-full min-w-0 mx-auto"
    x-data="{ dragId: null, viewsOpen: window.innerWidth >= 1024, mobileFilters: false }"
    x-effect="document.body.classList.toggle('overflow-hidden', mobileFilters)"
    x-init="window.addEventListener('resize', () => { if (window.innerWidth >= 1024) mobileFilters = false })"
    x-on:livewire:navigating.window="mobileFilters = false"
>
    {{-- ═══ HEADER ═══ --}}
    <div class="page-header">
        <div class="min-w-0">
            <h1 class="page-title">{{ __('pages.tickets.title') }}</h1>
            <p class="page-subtitle">
                @if($boxKey === 'trash')
                    {{ __('pages.tickets.subtitle_trash') }}
                @elseif($boxKey === 'archived')
                    {{ __('pages.tickets.subtitle_archived') }}
                @else
                    {{ __('pages.tickets.subtitle') }}
                @endif
            </p>
        </div>

        <div class="page-actions">
            {{-- View toggle (list / kanban) --}}
            @if($boxKey !== 'trash')
                <div class="view-toggle">
                    <button type="button" wire:click="setDisplayMode('list')"
                        class="view-toggle-btn {{ ($displayMode ?? 'list') === 'list' ? 'view-toggle-btn-active' : 'view-toggle-btn-default' }}">
                        <iconify-icon icon="solar:list-bold" width="16"></iconify-icon>
                    </button>
                    <button type="button" wire:click="setDisplayMode('kanban')"
                        class="view-toggle-btn {{ ($displayMode ?? 'list') === 'kanban' ? 'view-toggle-btn-active' : 'view-toggle-btn-default' }}">
                        <iconify-icon icon="solar:widget-4-bold" width="16"></iconify-icon>
                    </button>
                </div>
            @endif

            {{-- Sidebar toggle (desktop) --}}
            <button type="button"
                class="hidden lg:inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm font-semibold text-slate-600 shadow-sm hover:bg-slate-50 hover:text-slate-900 transition-all"
                @click="viewsOpen = !viewsOpen">
                <iconify-icon icon="solar:sidebar-minimalistic-bold-duotone" width="18"></iconify-icon>
            </button>

            {{-- Filters (mobile) --}}
            <button type="button"
                class="lg:hidden inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm font-semibold text-slate-600 shadow-sm hover:bg-slate-50 transition-all touch-target sm:min-h-0 sm:min-w-0"
                @click="mobileFilters = true">
                <iconify-icon icon="solar:filter-bold-duotone" width="18"></iconify-icon>
                <span class="hidden sm:inline">{{ __('Filtres') }}</span>
            </button>

            {{-- New ticket --}}
            <a href="{{ route('tickets.create') }}" wire:navigate
               class="inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2.5 sm:px-4 text-sm font-semibold text-white shadow-lg shadow-[var(--accent-ring)] hover:opacity-90 transition-all transform hover:-translate-y-0.5 touch-target sm:min-h-0 sm:min-w-0"
               style="background-color: var(--accent);">
                <iconify-icon icon="solar:add-circle-bold" width="18"></iconify-icon>
                <span class="hidden sm:inline">{{ __('pages.tickets.new_ticket') }}</span>
            </a>
        </div>
    </div>

    {{-- ═══ GROUP CONTEXT BAR ═══ --}}
    @if(($activeGroup ?? null) || ($group ?? '') === 'none')
        <div class="flex items-center gap-3 mb-5 px-4 py-3 rounded-xl bg-white" style="border: 1px solid #f1f5f9;">
            @if($activeGroup ?? null)
                <span class="h-3 w-3 rounded-full shrink-0" style="background-color: {{ $activeGroup->color ?? 'var(--accent)' }};"></span>
                <span class="text-sm font-bold text-slate-900">{{ $activeGroup->name }}</span>
            @else
                <iconify-icon icon="solar:minus-circle-bold-duotone" width="16" class="text-slate-400 shrink-0"></iconify-icon>
                <span class="text-sm font-bold text-slate-900">{{ __('pages.groups.ungrouped') }}</span>
            @endif
            <span class="text-slate-200">|</span>
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" type="button" class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-500 hover:text-slate-700 transition-colors">
                    {{ __('pages.groups.switch_group') }}
                    <iconify-icon icon="solar:alt-arrow-down-linear" width="12"></iconify-icon>
                </button>
                <div x-show="open" @click.away="open = false" x-transition
                     class="absolute left-0 top-full mt-1 w-48 rounded-xl border border-slate-100 bg-white shadow-lg z-50 py-1">
                    <button type="button" wire:click="$set('group', '')" @click="open = false"
                            class="w-full flex items-center gap-2 px-3 py-2 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                        <iconify-icon icon="solar:layers-bold-duotone" width="14"></iconify-icon>
                        {{ __('pages.tickets.all_groups') }}
                    </button>
                    @foreach(collect($ticketGroups ?? []) as $tg)
                        <button type="button" wire:click="$set('group', '{{ $tg->id }}')" @click="open = false"
                                class="w-full flex items-center gap-2 px-3 py-2 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                            <span class="h-2.5 w-2.5 rounded-full shrink-0" style="background-color: {{ $tg->color ?? 'var(--accent)' }};"></span>
                            {{ $tg->name }}
                        </button>
                    @endforeach
                    <button type="button" wire:click="$set('group', 'none')" @click="open = false"
                            class="w-full flex items-center gap-2 px-3 py-2 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                        <iconify-icon icon="solar:minus-circle-bold-duotone" width="14" class="text-slate-400"></iconify-icon>
                        {{ __('pages.tickets.no_group') }}
                    </button>
                </div>
            </div>
            <a href="{{ route('tickets.index') }}" wire:navigate class="ml-auto inline-flex items-center gap-1.5 text-xs font-medium text-slate-500 hover:text-[var(--accent)] transition-colors">
                <iconify-icon icon="solar:close-circle-linear" width="14"></iconify-icon>
                {{ __('pages.groups.all_tickets') }}
            </a>
        </div>
    @endif

    {{-- ═══ STATS CARDS ═══ --}}
    <div class="grid grid-cols-2 gap-3 sm:gap-4 mb-6 sm:mb-8 lg:grid-cols-4">
        <div class="stat-card">
            <div class="flex justify-between items-start gap-2">
                <div class="min-w-0">
                    <span class="stat-card-label">{{ __('Ouverts') }}</span>
                    <div class="stat-card-value">{{ $stats['open'] ?? 0 }}</div>
                </div>
                <div class="stat-card-icon bg-red-50 text-red-500">
                    <iconify-icon icon="solar:bolt-circle-bold-duotone" width="20"></iconify-icon>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex justify-between items-start gap-2">
                <div class="min-w-0">
                    <span class="stat-card-label">{{ __('En cours') }}</span>
                    <div class="stat-card-value">{{ $stats['in_progress'] ?? 0 }}</div>
                </div>
                <div class="stat-card-icon bg-blue-50 text-blue-500">
                    <iconify-icon icon="solar:clock-circle-bold-duotone" width="20"></iconify-icon>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex justify-between items-start gap-2">
                <div class="min-w-0">
                    <span class="stat-card-label">{{ __('En attente') }}</span>
                    <div class="stat-card-value">{{ $stats['pending'] ?? 0 }}</div>
                </div>
                <div class="stat-card-icon bg-amber-50 text-amber-500">
                    <iconify-icon icon="solar:hourglass-bold-duotone" width="20"></iconify-icon>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex justify-between items-start gap-2">
                <div class="min-w-0">
                    <span class="stat-card-label">{{ __('Résolus (7j)') }}</span>
                    <div class="stat-card-value">{{ $stats['resolved_7d'] ?? 0 }}</div>
                </div>
                <div class="stat-card-icon bg-emerald-50 text-emerald-500">
                    <iconify-icon icon="solar:check-circle-bold-duotone" width="20"></iconify-icon>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ MAIN LAYOUT (sidebar + content) ═══ --}}
    <div class="flex gap-6 lg:gap-8 min-w-0">

        {{-- ── SIDEBAR (desktop only, collapsible) ── --}}
        <div class="hidden lg:block shrink-0 transition-all duration-300"
             x-show="viewsOpen" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-x-4 w-0"
             x-transition:enter-end="opacity-100 translate-x-0 w-[16rem]"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 w-[16rem]"
             x-transition:leave-end="opacity-0 w-0"
             style="width: 16rem;">
            <div class="sticky top-24 space-y-4 w-[16rem]">
                {{-- Box toggle (active / archived / trash) --}}
                <div class="sidebar-panel">
                    <div class="p-2">
                        <div class="tab-bar">
                            <button type="button" wire:click="setBox('active')"
                                class="tab-bar-item {{ $boxKey === 'active' ? 'tab-bar-item-active' : 'tab-bar-item-default' }}">
                                <span class="inline-flex items-center gap-1.5 justify-center w-full truncate">
                                    <iconify-icon icon="solar:ticket-bold-duotone" width="15" class="shrink-0"></iconify-icon>
                                    <span class="truncate">{{ __('pages.tickets.active') }}</span>
                                </span>
                            </button>
                            <button type="button" wire:click="setBox('archived')"
                                class="tab-bar-item {{ $boxKey === 'archived' ? 'tab-bar-item-active' : 'tab-bar-item-default' }}">
                                <span class="inline-flex items-center gap-1.5 justify-center w-full truncate">
                                    <iconify-icon icon="solar:archive-bold-duotone" width="15" class="shrink-0"></iconify-icon>
                                    <span class="truncate">{{ __('pages.tickets.archived') }}</span>
                                </span>
                            </button>
                            @if($isStaff ?? false)
                            <button type="button" wire:click="setBox('trash')"
                                class="tab-bar-item {{ $boxKey === 'trash' ? 'tab-bar-item-active' : 'tab-bar-item-default' }}">
                                <span class="inline-flex items-center gap-1.5 justify-center w-full truncate">
                                    <iconify-icon icon="solar:trash-bin-trash-bold-duotone" width="15" class="shrink-0"></iconify-icon>
                                    <span class="truncate">{{ __('pages.tickets.trash') }}</span>
                                </span>
                            </button>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Quick views --}}
                <div class="sidebar-panel">
                    <div class="sidebar-panel-header">
                        <h3>{{ __('pages.tickets.quick_views') }}</h3>
                    </div>
                    <div class="sidebar-panel-body">
                        <button type="button" wire:click="setView('all')"
                            class="sidebar-item {{ $isActive('all') ? 'sidebar-item-active' : 'sidebar-item-default' }}">
                            <span class="flex items-center gap-2.5">
                                <iconify-icon icon="solar:layers-bold-duotone" width="17"></iconify-icon>
                                {{ __('Tous les tickets') }}
                            </span>
                            <span class="sidebar-badge {{ $isActive('all') ? 'sidebar-badge-active' : 'sidebar-badge-default' }}">{{ $viewCounts['all'] ?? 0 }}</span>
                        </button>
                        <button type="button" wire:click="setView('created_by_me')"
                            class="sidebar-item {{ $isActive('created_by_me') ? 'sidebar-item-active' : 'sidebar-item-default' }}">
                            <span class="flex items-center gap-2.5">
                                <iconify-icon icon="solar:pen-bold-duotone" width="17"></iconify-icon>
                                {{ __('Créés par moi') }}
                            </span>
                            <span class="sidebar-badge {{ $isActive('created_by_me') ? 'sidebar-badge-active' : 'sidebar-badge-default' }}">{{ $viewCounts['created_by_me'] ?? 0 }}</span>
                        </button>
                        <button type="button" wire:click="setView('assigned_to_me')"
                            class="sidebar-item {{ $isActive('assigned_to_me') ? 'sidebar-item-active' : 'sidebar-item-default' }}">
                            <span class="flex items-center gap-2.5">
                                <iconify-icon icon="solar:user-check-bold-duotone" width="17"></iconify-icon>
                                {{ __('Assignés à moi') }}
                            </span>
                            <span class="sidebar-badge {{ $isActive('assigned_to_me') ? 'sidebar-badge-active' : 'sidebar-badge-default' }}">{{ $viewCounts['assigned_to_me'] ?? 0 }}</span>
                        </button>
                        <button type="button" wire:click="setView('high_priority')"
                            class="sidebar-item {{ $isActive('high_priority') ? 'sidebar-item-active' : 'sidebar-item-default' }}">
                            <span class="flex items-center gap-2.5">
                                <iconify-icon icon="solar:danger-triangle-bold-duotone" width="17"></iconify-icon>
                                {{ __('Haute priorité') }}
                            </span>
                            <span class="sidebar-badge {{ $isActive('high_priority') ? 'sidebar-badge-active' : 'sidebar-badge-default' }}">{{ $viewCounts['high_priority'] ?? 0 }}</span>
                        </button>
                    </div>
                </div>

                {{-- Source --}}
                <div class="sidebar-panel">
                    <div class="sidebar-panel-header">
                        <h3>{{ __('pages.tickets.source') }}</h3>
                    </div>
                    <div class="sidebar-panel-body">
                        <button type="button" wire:click="setSource('all')"
                            class="sidebar-item {{ ($source ?? 'all') === 'all' ? 'sidebar-item-active' : 'sidebar-item-default' }}">
                            <span class="flex items-center gap-2.5">
                                <iconify-icon icon="solar:layers-bold-duotone" width="17"></iconify-icon>
                                {{ __('pages.tickets.source_all') }}
                            </span>
                            <span class="sidebar-badge {{ ($source ?? 'all') === 'all' ? 'sidebar-badge-active' : 'sidebar-badge-default' }}">{{ ($viewCounts['all'] ?? 0) }}</span>
                        </button>
                        <button type="button" wire:click="setSource('from_form')"
                            class="sidebar-item {{ ($source ?? 'all') === 'from_form' ? 'sidebar-item-active' : 'sidebar-item-default' }}">
                            <span class="flex items-center gap-2.5">
                                <iconify-icon icon="solar:document-text-bold-duotone" width="17"></iconify-icon>
                                {{ __('pages.tickets.source_from_form') }}
                            </span>
                            <span class="sidebar-badge {{ ($source ?? 'all') === 'from_form' ? 'sidebar-badge-active' : 'sidebar-badge-default' }}">{{ $viewCounts['from_form'] ?? 0 }}</span>
                        </button>
                        <button type="button" wire:click="setSource('from_platform')"
                            class="sidebar-item {{ ($source ?? 'all') === 'from_platform' ? 'sidebar-item-active' : 'sidebar-item-default' }}">
                            <span class="flex items-center gap-2.5">
                                <iconify-icon icon="solar:pen-new-square-bold-duotone" width="17"></iconify-icon>
                                {{ __('pages.tickets.source_from_platform') }}
                            </span>
                            <span class="sidebar-badge {{ ($source ?? 'all') === 'from_platform' ? 'sidebar-badge-active' : 'sidebar-badge-default' }}">{{ $viewCounts['from_platform'] ?? 0 }}</span>
                        </button>
                    </div>
                </div>

                {{-- Groups --}}
                @if(($ticketGroups ?? collect())->isNotEmpty())
                <div class="sidebar-panel">
                    <div class="sidebar-panel-header">
                        <h3>{{ __('pages.tickets.groups') }}</h3>
                    </div>
                    <div class="sidebar-panel-body">
                        <button type="button" wire:click="$set('group', '')"
                            class="sidebar-item {{ ($group ?? '') === '' ? 'sidebar-item-active' : 'sidebar-item-default' }}">
                            <span class="flex items-center gap-2.5">
                                <iconify-icon icon="solar:layers-bold-duotone" width="17"></iconify-icon>
                                {{ __('pages.tickets.all_groups') }}
                            </span>
                        </button>
                        @foreach($ticketGroups as $tg)
                            <button type="button" wire:click="$set('group', '{{ $tg->id }}')"
                                class="sidebar-item {{ ($group ?? '') === (string) $tg->id ? 'sidebar-item-active' : 'sidebar-item-default' }}">
                                <span class="flex items-center gap-2.5">
                                    <span class="h-2.5 w-2.5 rounded-full shrink-0" style="background-color: {{ $tg->color ?? 'var(--accent)' }};"></span>
                                    {{ $tg->name }}
                                </span>
                                <span class="sidebar-badge {{ ($group ?? '') === (string) $tg->id ? 'sidebar-badge-active' : 'sidebar-badge-default' }}">{{ $groupCounts[$tg->id] ?? 0 }}</span>
                            </button>
                        @endforeach
                        <button type="button" wire:click="$set('group', 'none')"
                            class="sidebar-item {{ ($group ?? '') === 'none' ? 'sidebar-item-active' : 'sidebar-item-default' }}">
                            <span class="flex items-center gap-2.5">
                                <iconify-icon icon="solar:minus-circle-bold-duotone" width="17" class="text-slate-400"></iconify-icon>
                                {{ __('pages.tickets.no_group') }}
                            </span>
                        </button>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- ── MAIN CONTENT ── --}}
        <div class="flex-1 min-w-0">

            @if(($loadStage ?? 0) >= 2)

            @if (($displayMode ?? 'list') === 'kanban' && $boxKey !== 'trash')
                {{-- ═══ KANBAN VIEW ═══ --}}
                <div class="content-card h-[calc(100vh-14rem)] sm:h-[calc(100vh-12rem)] min-h-[400px] flex flex-col">
                    <div class="shrink-0 p-3 sm:p-4 border-b border-slate-100/80 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between bg-slate-50/30">
                        <h2 class="text-sm font-bold text-slate-900">{{ __('pages.tickets.kanban_board') }}</h2>
                        <div class="text-xs text-slate-400 hidden sm:block">{{ __('pages.tickets.drag_to_change_status') }}</div>
                    </div>
                    <div class="flex-1 min-h-0 min-w-0 p-3 sm:p-4 overflow-x-auto overflow-y-hidden custom-scrollbar scroll-touch">
                        <div class="flex gap-3 sm:gap-4 h-full min-w-max pb-2">
                            @foreach (($statusColumns ?? []) as $colStatus)
                                @php
                                    [$colLabel, $colIcon] = $statusLabel($colStatus);
                                    $colPill = $statusPill($colStatus);
                                    $cards = $kanbanTickets[$colStatus] ?? [];
                                @endphp
                                <div class="w-[272px] sm:w-[300px] kanban-column"
                                    @dragover.prevent
                                    @drop.prevent="if (dragId) { $wire.moveTicket(dragId, '{{ $colStatus }}'); dragId = null; }">
                                    <div class="kanban-column-header">
                                        <span class="pill-badge {{ $colPill['bg'] }} {{ $colPill['text'] }} {{ $colPill['border'] }}">
                                            {{ $colLabel }}
                                        </span>
                                        <span class="text-xs font-bold text-slate-300">{{ count($cards) }}</span>
                                    </div>
                                    <div class="p-2.5 space-y-2.5 overflow-y-auto custom-scrollbar flex-1">
                                        @forelse ($cards as $t)
                                            @php
                                                $prio = $priorityMeta($t->priority?->level);
                                                $prog = $checklistProgress[$t->id] ?? null;
                                                $pct = $prog && (int) $prog->total > 0 ? (int) round(100 * (int) $prog->done / (int) $prog->total) : null;
                                            @endphp
                                            <div x-data="{ dragging: false }"
                                                class="kanban-card group"
                                                draggable="true"
                                                @mousedown="dragging = false"
                                                @mousemove="dragging = true"
                                                @click="if (!dragging) Livewire.navigate('{{ $t->public_id ? url('/tickets/' . e($t->public_id)) : '#' }}')"
                                                @dragstart="dragId = {{ (int) $t->id }}"
                                                @dragend="dragId = null">
                                                <div class="flex justify-between items-start mb-2">
                                                    <span class="text-[11px] font-mono font-bold text-slate-400">{{ $t->shortReference() }}</span>
                                                    <span class="h-2 w-2 rounded-full {{ $prio['dot'] }}" title="{{ $prio['label'] }}"></span>
                                                </div>
                                                <h4 class="text-sm font-bold text-slate-900 mb-2 line-clamp-2 group-hover:text-[var(--accent)] transition-colors leading-snug">{{ $t->subject }}</h4>
                                                <div class="flex items-center gap-1.5 mb-2 flex-wrap">
                                                    @if ($t->group)
                                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-medium border" style="background-color: {{ $t->group->color ?? 'var(--accent)' }}15; color: {{ $t->group->color ?? 'var(--accent)' }}; border-color: {{ $t->group->color ?? 'var(--accent)' }}30;">
                                                            <span class="h-1.5 w-1.5 rounded-full" style="background-color: {{ $t->group->color ?? 'var(--accent)' }};"></span>
                                                            {{ $t->group->name }}
                                                        </span>
                                                    @endif
                                                    @if ($t->formResponse)
                                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-medium bg-violet-50 text-violet-600 border border-violet-100/80">
                                                            <iconify-icon icon="solar:document-text-bold-duotone" width="10"></iconify-icon>
                                                            {{ __('pages.tickets.source_label_form') }}
                                                        </span>
                                                    @endif
                                                    @if ($t->sla_policy_id)
                                                        <x-sla-badge :status="$t->slaFirstResponseStatus()" type="fr" />
                                                        <x-sla-badge :status="$t->slaResolutionStatus()" type="res" />
                                                    @endif
                                                    @if($t->approval_status === 'pending')
                                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-200/60">
                                                            <iconify-icon icon="solar:shield-check-linear" width="10"></iconify-icon>
                                                            Validation
                                                        </span>
                                                    @endif
                                                </div>
                                                @if ($pct !== null)
                                                    <div class="mb-2">
                                                        <div class="flex items-center gap-2">
                                                            <div class="flex-1 h-1.5 rounded-full bg-slate-100 overflow-hidden">
                                                                <div class="h-full rounded-full transition-all {{ $pct >= 100 ? 'bg-emerald-500' : 'bg-slate-400' }}" style="width: {{ $pct }}%"></div>
                                                            </div>
                                                            <span class="text-[10px] font-bold {{ $pct >= 100 ? 'text-emerald-600' : 'text-slate-400' }}">{{ $pct }}%</span>
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="flex items-center justify-between mt-3 pt-3 border-t border-slate-50">
                                                    <div class="flex items-center gap-2">
                                                        <x-avatar :name="$t->creator?->name ?? 'U'" size="h-5 w-5" class="ring-1 ring-white shadow-sm" />
                                                        <span class="text-[11px] text-slate-500 truncate max-w-[100px]">{{ $t->creator?->name }}</span>
                                                    </div>
                                                    <span class="text-[10px] text-slate-300">{{ $t->updated_at?->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="py-8 text-center text-xs text-slate-300 italic">{{ __('pages.tickets.empty_column') }}</div>
                                        @endforelse
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                {{-- ═══ LIST VIEW ═══ --}}
                @php
                    $ticketStatusFilterOptions = [
                        ['value' => '', 'label' => __('pages.dashboard.status')],
                        ['value' => 'open', 'label' => __('Ouvert')],
                        ['value' => 'in_progress', 'label' => __('En cours')],
                        ['value' => 'pending', 'label' => __('En attente')],
                        ['value' => 'resolved', 'label' => __('Résolu')],
                        ['value' => 'closed', 'label' => __('Fermé')],
                    ];
                    $ticketStatusFilterLabel = $status === ''
                        ? __('pages.dashboard.status')
                        : (collect($ticketStatusFilterOptions)->firstWhere('value', $status)['label'] ?? $status);
                    $ticketPriorityFilterOptions = [['value' => '', 'label' => __('pages.dashboard.priority')]];
                    foreach ($priorities as $p) {
                        $ticketPriorityFilterOptions[] = ['value' => (string) $p->id, 'label' => $p->name];
                    }
                    $ticketPriorityFilterLabel = $priority === ''
                        ? __('pages.dashboard.priority')
                        : ($priorities->firstWhere('id', (int) $priority)?->name ?? $priority);
                @endphp
                <div class="content-card">
                    {{-- Filter bar --}}
                    <div class="filter-bar">
                        <div class="filter-bar-row">
                            <div class="flex-1 min-w-0 relative">
                                <iconify-icon icon="solar:magnifer-linear" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-300 pointer-events-none" width="18"></iconify-icon>
                                <input type="text" class="filter-search"
                                    placeholder="{{ __('pages.tickets.search_placeholder') }}"
                                    wire:model.live="search" />
                            </div>
                            <div class="filter-controls">
                                <div class="w-full min-w-0 sm:w-36 flex-1 sm:flex-none">
                                    <x-select-input
                                        :options="$ticketStatusFilterOptions"
                                        :label="$ticketStatusFilterLabel"
                                        :selected-value="$status"
                                        wire:model.live="status"
                                    />
                                </div>
                                <div class="w-full min-w-0 sm:w-36 flex-1 sm:flex-none">
                                    <x-select-input
                                        :options="$ticketPriorityFilterOptions"
                                        :label="$ticketPriorityFilterLabel"
                                        :selected-value="$priority"
                                        wire:model.live="priority"
                                    />
                                </div>
                                <button wire:click="resetFilters" class="filter-reset touch-target sm:min-h-0">
                                    <iconify-icon icon="solar:restart-linear" width="16" class="text-slate-400"></iconify-icon>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Table --}}
                    <div class="responsive-table-wrap scroll-touch">
                        <table class="data-table min-w-[680px] sm:min-w-[760px]">
                            <thead>
                                <tr>
                                    <th class="min-w-[200px] sm:min-w-[260px] lg:min-w-[300px]">{{ __('pages.dashboard.subject') }}</th>
                                    <th>{{ __('Catégorie') }}</th>
                                    @if(($ticketGroups ?? collect())->isNotEmpty())
                                    <th>{{ __('pages.tickets.groups') }}</th>
                                    @endif
                                    <th>{{ __('pages.dashboard.priority') }}</th>
                                    <th>{{ __('pages.dashboard.status') }}</th>
                                    <th class="text-right">{{ __('pages.tickets.activity') }}</th>
                                    @if($boxKey === 'trash')
                                        <th class="text-right">{{ __('pages.tickets.actions') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tickets as $t)
                                    @php
                                        [$label, $icon] = $statusLabel($t->status->value);
                                        $pill = $statusPill($t->status->value);
                                        $prio = $priorityMeta($t->priority?->level);
                                    @endphp
                                    <tr class="{{ $boxKey !== 'trash' ? 'cursor-pointer' : '' }}" @if($boxKey !== 'trash' && $t->public_id) onclick="Livewire.navigate('{{ url('/tickets/' . e($t->public_id)) }}')" @endif>
                                        <td class="min-w-[200px] sm:min-w-[260px] lg:min-w-[300px]">
                                            <div class="flex items-start sm:items-center gap-3 sm:gap-4 min-w-0">
                                                <span class="shrink-0 inline-flex items-center justify-center rounded-lg px-2 py-1.5 sm:px-2.5 sm:py-1.5 text-[11px] sm:text-xs font-semibold font-mono tracking-tight bg-[var(--accent-soft)] text-[var(--accent)]">
                                                    {{ $t->shortReference() }}
                                                </span>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex flex-wrap items-baseline gap-2 gap-y-1">
                                                        <span class="text-sm font-bold text-slate-900 group-hover:text-[var(--accent)] transition-colors break-words line-clamp-2 leading-snug">{{ $t->subject }}</span>
                                                        @php $prog = $checklistProgress[$t->id] ?? null; @endphp
                                                        @if ($prog && (int) $prog->total > 0)
                                                            @php $pct = (int) round(100 * (int) $prog->done / (int) $prog->total); @endphp
                                                            <span class="shrink-0 inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-bold {{ $pct >= 100 ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                                                <iconify-icon icon="solar:checklist-minimalistic-linear" width="11"></iconify-icon>
                                                                {{ $pct }}%
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="text-xs text-slate-400 mt-1 flex items-center gap-1 flex-wrap">
                                                        <span class="truncate">{{ $t->creator?->name ?? __('pages.tickets.unknown_user') }}</span>
                                                        @if($t->assignees->isNotEmpty())
                                                            <span class="text-slate-200">·</span>
                                                            <span class="flex items-center gap-1">
                                                                <span class="flex -space-x-1.5">
                                                                    @foreach($t->assignees->take(3) as $a)
                                                                        <span class="inline-flex h-5 w-5 items-center justify-center rounded-full text-[9px] font-bold ring-2 ring-white shrink-0" style="background: var(--accent-soft); color: var(--accent);" title="{{ $a->name }}">{{ strtoupper(mb_substr($a->name, 0, 1)) }}</span>
                                                                    @endforeach
                                                                </span>
                                                                @if($t->assignees->count() > 3)
                                                                    <span class="text-[10px] font-bold text-slate-400">+{{ $t->assignees->count() - 3 }}</span>
                                                                @endif
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-sm text-slate-500 whitespace-nowrap">
                                            {{ $t->category?->name ?? '—' }}
                                        </td>
                                        @if(($ticketGroups ?? collect())->isNotEmpty())
                                        <td class="whitespace-nowrap">
                                            @if($t->group)
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg text-xs font-medium border" style="background-color: {{ $t->group->color ?? 'var(--accent)' }}15; color: {{ $t->group->color ?? 'var(--accent)' }}; border-color: {{ $t->group->color ?? 'var(--accent)' }}30;">
                                                    <span class="h-1.5 w-1.5 rounded-full" style="background-color: {{ $t->group->color ?? 'var(--accent)' }};"></span>
                                                    {{ $t->group->name }}
                                                </span>
                                            @else
                                                <span class="text-xs text-slate-300">—</span>
                                            @endif
                                        </td>
                                        @endif
                                        <td>
                                            <div class="flex items-center gap-2">
                                                <span class="h-2 w-2 shrink-0 rounded-full {{ $prio['dot'] }}"></span>
                                                <span class="text-sm font-medium {{ $prio['text'] }}">{{ $t->priority?->name ?? $prio['label'] }}</span>
                                            </div>
                                            @if($t->sla_policy_id)
                                                <div class="flex items-center gap-1 mt-1">
                                                    <x-sla-badge :status="$t->slaFirstResponseStatus()" type="fr" />
                                                    <x-sla-badge :status="$t->slaResolutionStatus()" type="res" />
                                                </div>
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap">
                                            <span class="pill-badge {{ $pill['bg'] }} {{ $pill['text'] }} {{ $pill['border'] }}">
                                                <iconify-icon icon="{{ $icon }}" width="14"></iconify-icon>
                                                {{ $label }}
                                            </span>
                                            @if($t->approval_status === 'pending')
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-200/60 ml-1">
                                                    <iconify-icon icon="solar:shield-check-linear" width="11"></iconify-icon>
                                                    Validation
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-right text-sm text-slate-400 whitespace-nowrap">
                                            {{ $boxKey === 'trash' ? ($t->deleted_at?->diffForHumans() ?? '—') : $t->updated_at?->diffForHumans() }}
                                        </td>
                                        @if($boxKey === 'trash')
                                            <td class="text-right whitespace-nowrap">
                                                <div class="flex items-center justify-end gap-2">
                                                    <button type="button" wire:click="restoreFromTrash({{ $t->id }})" data-restore class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 min-h-[44px] sm:min-h-0 sm:py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors touch-manipulation">
                                                        <iconify-icon icon="solar:restart-bold-duotone" width="14"></iconify-icon>
                                                        {{ __('pages.tickets.restore') }}
                                                    </button>
                                                    <button type="button" @click="$dispatch('confirm-action', { title: '{{ __('pages.tickets.force_delete') }}', message: '{{ __('pages.tickets.force_delete_confirm') }}', confirmLabel: '{{ __('pages.tickets.force_delete') }}', variant: 'danger', onConfirm: () => $wire.forceDeleteTicket({{ $t->id }}) })" class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-red-50 px-3 py-2 min-h-[44px] sm:min-h-0 sm:py-1.5 text-xs font-semibold text-red-600 hover:bg-red-100 transition-colors touch-manipulation">
                                                        <iconify-icon icon="solar:trash-bin-trash-bold" width="14"></iconify-icon>
                                                        {{ __('pages.tickets.force_delete') }}
                                                    </button>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ ($boxKey === 'trash' ? 6 : 5) + (($ticketGroups ?? collect())->isNotEmpty() ? 1 : 0) }}">
                                            <div class="empty-state">
                                                <div class="empty-state-icon">
                                                    <iconify-icon icon="solar:ticket-linear" width="28" class="text-slate-300"></iconify-icon>
                                                </div>
                                                <p class="empty-state-title">{{ __('pages.tickets.no_tickets_found') }}</p>
                                                <p class="empty-state-text">{{ __('pages.tickets.no_tickets_try_filters') }}</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($tickets->hasPages())
                    <div class="px-4 sm:px-6 py-4 border-t border-slate-50 bg-slate-50/20 overflow-x-auto">
                        {{ $tickets->links() }}
                    </div>
                    @endif
                </div>
            @endif

            @else
            {{-- Skeleton --}}
            <div class="content-card">
                <div class="p-4 sm:p-6 animate-pulse space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="h-9 bg-slate-100 rounded-lg w-64"></div>
                        <div class="h-9 bg-slate-100 rounded-lg w-32"></div>
                        <div class="ml-auto h-9 bg-slate-100 rounded-lg w-24"></div>
                    </div>
                    @for($i = 0; $i < 6; $i++)
                    <div class="flex items-center gap-4 py-3 border-b border-slate-50">
                        <div class="h-4 bg-slate-200 rounded w-16"></div>
                        <div class="flex-1 space-y-1.5">
                            <div class="h-4 bg-slate-100 rounded w-3/4"></div>
                            <div class="h-3 bg-slate-50 rounded w-1/3"></div>
                        </div>
                        <div class="h-6 bg-slate-50 rounded-full w-20"></div>
                        <div class="h-6 bg-slate-50 rounded-full w-16"></div>
                    </div>
                    @endfor
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- ═══ MOBILE FILTERS DRAWER ═══ --}}
    <template x-teleport="body">
    <div x-show="mobileFilters" x-cloak class="lg:hidden fixed inset-0 z-[60]" style="display: none;">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
             @click="mobileFilters = false"
             x-show="mobileFilters"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"></div>
        <div class="absolute left-0 top-0 bottom-0 w-full max-w-[min(100%,20rem)] bg-white shadow-2xl flex flex-col rounded-r-2xl overflow-hidden"
             x-show="mobileFilters"
             x-transition:enter="transform transition ease-out duration-300"
             x-transition:enter-start="-translate-x-full opacity-0" x-transition:enter-end="translate-x-0 opacity-100"
             x-transition:leave="transform transition ease-in duration-200"
             x-transition:leave-start="translate-x-0 opacity-100" x-transition:leave-end="-translate-x-full opacity-0">

            {{-- Drawer header --}}
            <div class="shrink-0 flex items-center justify-between px-5 py-4" style="border-bottom: 1px solid #f1f5f9;">
                <h2 class="text-base font-bold text-slate-900">{{ __('Filtres') }}</h2>
                <button @click="mobileFilters = false" class="h-9 w-9 rounded-xl text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors flex items-center justify-center touch-manipulation">
                    <iconify-icon icon="solar:close-circle-bold" width="22"></iconify-icon>
                </button>
            </div>

            {{-- Drawer body --}}
            <div class="flex-1 overflow-y-auto overscroll-contain custom-scrollbar p-4 space-y-4" @touchmove.stop>
                {{-- Box toggle --}}
                <div class="tab-bar">
                    <button type="button" wire:click="setBox('active')" @click="mobileFilters = false"
                        class="tab-bar-item {{ $boxKey === 'active' ? 'tab-bar-item-active' : 'tab-bar-item-default' }}">
                        {{ __('pages.tickets.active') }}
                    </button>
                    <button type="button" wire:click="setBox('archived')" @click="mobileFilters = false"
                        class="tab-bar-item {{ $boxKey === 'archived' ? 'tab-bar-item-active' : 'tab-bar-item-default' }}">
                        {{ __('pages.tickets.archived') }}
                    </button>
                    @if($isStaff ?? false)
                    <button type="button" wire:click="setBox('trash')" @click="mobileFilters = false"
                        class="tab-bar-item {{ $boxKey === 'trash' ? 'tab-bar-item-active' : 'tab-bar-item-default' }}">
                        {{ __('pages.tickets.trash') }}
                    </button>
                    @endif
                </div>

                {{-- Quick views --}}
                <div>
                    <h3 class="text-[10px] font-bold uppercase tracking-[0.08em] text-slate-400 mb-2 px-1">{{ __('pages.tickets.quick_views') }}</h3>
                    <div class="space-y-0.5">
                        @foreach(['all' => __('Tous les tickets'), 'created_by_me' => __('Créés par moi'), 'assigned_to_me' => __('Assignés à moi'), 'high_priority' => __('Haute priorité')] as $vk => $vl)
                            <button type="button" wire:click="setView('{{ $vk }}')" @click="mobileFilters = false"
                                class="sidebar-item {{ $isActive($vk) ? 'sidebar-item-active' : 'sidebar-item-default' }}">
                                <span>{{ $vl }}</span>
                                <span class="sidebar-badge {{ $isActive($vk) ? 'sidebar-badge-active' : 'sidebar-badge-default' }}">{{ $viewCounts[$vk] ?? 0 }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Source --}}
                <div>
                    <h3 class="text-[10px] font-bold uppercase tracking-[0.08em] text-slate-400 mb-2 px-1">{{ __('pages.tickets.source') }}</h3>
                    <div class="space-y-0.5">
                        @foreach(['all' => __('pages.tickets.source_all'), 'from_form' => __('pages.tickets.source_from_form'), 'from_platform' => __('pages.tickets.source_from_platform')] as $sk => $sl)
                            <button type="button" wire:click="setSource('{{ $sk }}')" @click="mobileFilters = false"
                                class="sidebar-item {{ ($source ?? 'all') === $sk ? 'sidebar-item-active' : 'sidebar-item-default' }}">
                                <span>{{ $sl }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Groups --}}
                @if(($ticketGroups ?? collect())->isNotEmpty())
                <div>
                    <h3 class="text-[10px] font-bold uppercase tracking-[0.08em] text-slate-400 mb-2 px-1">{{ __('pages.tickets.groups') }}</h3>
                    <div class="space-y-0.5">
                        <button type="button" wire:click="$set('group', '')" @click="mobileFilters = false"
                            class="sidebar-item {{ ($group ?? '') === '' ? 'sidebar-item-active' : 'sidebar-item-default' }}">
                            {{ __('pages.tickets.all_groups') }}
                        </button>
                        @foreach($ticketGroups as $tg)
                            <button type="button" wire:click="$set('group', '{{ $tg->id }}')" @click="mobileFilters = false"
                                class="sidebar-item {{ ($group ?? '') === (string) $tg->id ? 'sidebar-item-active' : 'sidebar-item-default' }}">
                                <span class="flex items-center gap-2">
                                    <span class="h-2.5 w-2.5 rounded-full shrink-0" style="background-color: {{ $tg->color ?? 'var(--accent)' }};"></span>
                                    {{ $tg->name }}
                                </span>
                                <span class="sidebar-badge sidebar-badge-default">{{ $groupCounts[$tg->id] ?? 0 }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    </template>
</div>
