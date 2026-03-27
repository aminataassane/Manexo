<div class="w-full max-w-full min-w-0 mx-auto">
    {{-- ═══ HEADER ═══ --}}
    <div class="page-header">
        <div class="min-w-0">
            <h1 class="page-title">{{ __('pages.groups.title') }}</h1>
            <p class="page-subtitle">{{ __('pages.groups.subtitle') }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('tickets.index') }}" wire:navigate
               class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2.5 sm:px-4 text-sm font-semibold text-slate-600 shadow-sm hover:bg-slate-50 hover:text-slate-900 transition-all touch-target sm:min-h-0 sm:min-w-0">
                <iconify-icon icon="solar:ticket-bold-duotone" width="18"></iconify-icon>
                <span class="hidden sm:inline">{{ __('pages.groups.all_tickets') }}</span>
            </a>
        </div>
    </div>

    {{-- ═══ STATS CARDS ═══ --}}
    @if($groups->isNotEmpty() || $ungroupedStats)
        <div class="grid grid-cols-2 gap-3 sm:gap-4 mb-6 sm:mb-8 lg:grid-cols-5">
            <div class="stat-card">
                <div class="flex justify-between items-start gap-2">
                    <div class="min-w-0">
                        <span class="stat-card-label">{{ __('pages.groups.stats_groups') }}</span>
                        <div class="stat-card-value">{{ $globalStats['total_groups'] }}</div>
                    </div>
                    <div class="stat-card-icon bg-violet-50 text-violet-500">
                        <iconify-icon icon="solar:widget-5-bold-duotone" width="20"></iconify-icon>
                    </div>
                </div>
            </div>
            <div class="stat-card">
                <div class="flex justify-between items-start gap-2">
                    <div class="min-w-0">
                        <span class="stat-card-label">{{ __('pages.groups.open') }}</span>
                        <div class="stat-card-value">{{ $globalStats['open'] }}</div>
                    </div>
                    <div class="stat-card-icon bg-red-50 text-red-500">
                        <iconify-icon icon="solar:bolt-circle-bold-duotone" width="20"></iconify-icon>
                    </div>
                </div>
            </div>
            <div class="stat-card">
                <div class="flex justify-between items-start gap-2">
                    <div class="min-w-0">
                        <span class="stat-card-label">{{ __('pages.groups.in_progress') }}</span>
                        <div class="stat-card-value">{{ $globalStats['in_progress'] }}</div>
                    </div>
                    <div class="stat-card-icon bg-blue-50 text-blue-500">
                        <iconify-icon icon="solar:clock-circle-bold-duotone" width="20"></iconify-icon>
                    </div>
                </div>
            </div>
            <div class="stat-card">
                <div class="flex justify-between items-start gap-2">
                    <div class="min-w-0">
                        <span class="stat-card-label">{{ __('pages.groups.pending') }}</span>
                        <div class="stat-card-value">{{ $globalStats['pending'] }}</div>
                    </div>
                    <div class="stat-card-icon bg-amber-50 text-amber-500">
                        <iconify-icon icon="solar:hourglass-bold-duotone" width="20"></iconify-icon>
                    </div>
                </div>
            </div>
            <div class="stat-card col-span-2 sm:col-span-1">
                <div class="flex justify-between items-start gap-2">
                    <div class="min-w-0">
                        <span class="stat-card-label">{{ __('pages.groups.closed') }}</span>
                        <div class="stat-card-value">{{ $globalStats['closed'] }}</div>
                    </div>
                    <div class="stat-card-icon bg-emerald-50 text-emerald-500">
                        <iconify-icon icon="solar:check-circle-bold-duotone" width="20"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($groups->isEmpty() && !$ungroupedStats)
        {{-- ═══ EMPTY STATE ═══ --}}
        <div class="content-card">
            <div class="empty-state py-16">
                <div class="empty-state-icon">
                    <iconify-icon icon="solar:folder-with-files-bold-duotone" width="28" class="text-slate-300"></iconify-icon>
                </div>
                <p class="empty-state-title">{{ __('pages.groups.no_groups') }}</p>
                <p class="empty-state-text">{{ __('pages.groups.no_groups_hint') }}</p>
                @if($canSeeSettings)
                    <a href="{{ route('admin.settings') }}" class="mt-5 inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold text-white shadow-lg hover:opacity-90 transition-all" style="background-color: var(--accent);">
                        <iconify-icon icon="solar:settings-bold-duotone" width="18"></iconify-icon>
                        {{ __('menu.settings') }}
                    </a>
                @endif
            </div>
        </div>
    @else
        {{-- ═══ GROUPS GRID ═══ --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
            @foreach($groups as $grp)
                @php
                    $totalActive = (int) $grp->open_count + (int) $grp->in_progress_count + (int) $grp->pending_count;
                    $color = $grp->color ?? 'var(--accent)';
                @endphp
                <a href="{{ route('tickets.index', ['group' => $grp->id]) }}" wire:navigate
                   class="group relative overflow-hidden rounded-2xl bg-white transition-all duration-300 hover:-translate-y-0.5"
                   style="border: 1px solid #f1f5f9; box-shadow: 0 1px 3px 0 rgba(0,0,0,0.03);">
                    {{-- Color accent bar --}}
                    <div class="h-1 w-full" style="background-color: {{ $color }};"></div>

                    <div class="p-5">
                        {{-- Group identity --}}
                        <div class="flex items-center gap-3 mb-4">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl text-white text-sm font-bold" style="background-color: {{ $color }};">
                                @if($grp->icon)
                                    <iconify-icon icon="{{ $grp->icon }}" width="20"></iconify-icon>
                                @else
                                    {{ strtoupper(mb_substr($grp->name, 0, 1)) }}
                                @endif
                            </span>
                            <div class="min-w-0 flex-1">
                                <h3 class="text-sm font-bold text-slate-900 group-hover:text-[var(--accent)] transition-colors truncate">{{ $grp->name }}</h3>
                                <span class="text-xs text-slate-400">{{ $totalActive }} {{ __('pages.groups.total_active') }}</span>
                            </div>
                            <iconify-icon icon="solar:alt-arrow-right-linear" width="16" class="text-slate-200 group-hover:text-[var(--accent)] group-hover:translate-x-0.5 transition-all shrink-0"></iconify-icon>
                        </div>

                        {{-- Mini stats grid --}}
                        <div class="grid grid-cols-2 gap-2">
                            <div class="rounded-xl bg-red-50/60 px-3 py-2">
                                <span class="text-[10px] font-semibold uppercase tracking-wider text-red-400">{{ __('pages.groups.open') }}</span>
                                <div class="text-lg font-bold text-red-600">{{ (int) $grp->open_count }}</div>
                            </div>
                            <div class="rounded-xl bg-blue-50/60 px-3 py-2">
                                <span class="text-[10px] font-semibold uppercase tracking-wider text-blue-400">{{ __('pages.groups.in_progress') }}</span>
                                <div class="text-lg font-bold text-blue-600">{{ (int) $grp->in_progress_count }}</div>
                            </div>
                            <div class="rounded-xl bg-amber-50/60 px-3 py-2">
                                <span class="text-[10px] font-semibold uppercase tracking-wider text-amber-400">{{ __('pages.groups.pending') }}</span>
                                <div class="text-lg font-bold text-amber-600">{{ (int) $grp->pending_count }}</div>
                            </div>
                            <div class="rounded-xl bg-emerald-50/60 px-3 py-2">
                                <span class="text-[10px] font-semibold uppercase tracking-wider text-emerald-400">{{ __('pages.groups.closed') }}</span>
                                <div class="text-lg font-bold text-emerald-600">{{ (int) $grp->closed_count }}</div>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach

            {{-- Ungrouped card --}}
            @if($ungroupedStats)
                @php
                    $ungroupedTotal = (int) $ungroupedStats->open_count + (int) $ungroupedStats->in_progress_count + (int) $ungroupedStats->pending_count;
                @endphp
                <a href="{{ route('tickets.index', ['group' => 'none']) }}" wire:navigate
                   class="group relative overflow-hidden rounded-2xl bg-white transition-all duration-300 hover:-translate-y-0.5"
                   style="border: 1px dashed #e2e8f0; box-shadow: 0 1px 3px 0 rgba(0,0,0,0.02);">
                    <div class="h-1 w-full bg-slate-200"></div>

                    <div class="p-5">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-400 text-sm font-bold">
                                <iconify-icon icon="solar:minus-circle-bold-duotone" width="20"></iconify-icon>
                            </span>
                            <div class="min-w-0 flex-1">
                                <h3 class="text-sm font-bold text-slate-900 group-hover:text-[var(--accent)] transition-colors truncate">{{ __('pages.groups.ungrouped') }}</h3>
                                <span class="text-xs text-slate-400">{{ $ungroupedTotal }} {{ __('pages.groups.total_active') }}</span>
                            </div>
                            <iconify-icon icon="solar:alt-arrow-right-linear" width="16" class="text-slate-200 group-hover:text-[var(--accent)] group-hover:translate-x-0.5 transition-all shrink-0"></iconify-icon>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div class="rounded-xl bg-red-50/60 px-3 py-2">
                                <span class="text-[10px] font-semibold uppercase tracking-wider text-red-400">{{ __('pages.groups.open') }}</span>
                                <div class="text-lg font-bold text-red-600">{{ (int) $ungroupedStats->open_count }}</div>
                            </div>
                            <div class="rounded-xl bg-blue-50/60 px-3 py-2">
                                <span class="text-[10px] font-semibold uppercase tracking-wider text-blue-400">{{ __('pages.groups.in_progress') }}</span>
                                <div class="text-lg font-bold text-blue-600">{{ (int) $ungroupedStats->in_progress_count }}</div>
                            </div>
                            <div class="rounded-xl bg-amber-50/60 px-3 py-2">
                                <span class="text-[10px] font-semibold uppercase tracking-wider text-amber-400">{{ __('pages.groups.pending') }}</span>
                                <div class="text-lg font-bold text-amber-600">{{ (int) $ungroupedStats->pending_count }}</div>
                            </div>
                            <div class="rounded-xl bg-emerald-50/60 px-3 py-2">
                                <span class="text-[10px] font-semibold uppercase tracking-wider text-emerald-400">{{ __('pages.groups.closed') }}</span>
                                <div class="text-lg font-bold text-emerald-600">{{ (int) $ungroupedStats->closed_count }}</div>
                            </div>
                        </div>
                    </div>
                </a>
            @endif
        </div>
    @endif
</div>
