<div class="w-full max-w-full min-w-0 mx-auto">
    <!-- HEADER -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6 sm:mb-8">
        <div class="min-w-0">
            <h1 class="text-xl font-bold text-slate-900 tracking-tight sm:text-2xl lg:text-3xl">{{ __('pages.groups.title') }}</h1>
            <p class="mt-1 text-xs sm:text-sm text-slate-500">{{ __('pages.groups.subtitle') }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2 sm:gap-3">
            <a href="{{ route('tickets.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2.5 sm:px-4 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-all">
                <iconify-icon icon="solar:ticket-bold-duotone" width="18"></iconify-icon>
                {{ __('pages.groups.all_tickets') }}
            </a>
        </div>
    </div>

    <!-- STATS CARDS -->
    @if($groups->isNotEmpty() || $ungroupedStats)
        <div class="grid grid-cols-2 gap-3 sm:gap-4 mb-6 sm:mb-8 lg:grid-cols-5 min-[1920px]:gap-6">
            <!-- Total groups -->
            <div class="group relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 min-w-0">
                <div class="flex justify-between items-start gap-2">
                    <div class="min-w-0">
                        <span class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-slate-500 truncate block">{{ __('pages.groups.stats_groups') }}</span>
                        <div class="mt-1 sm:mt-2 text-2xl sm:text-3xl font-bold text-slate-900">{{ $globalStats['total_groups'] }}</div>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-50 text-violet-600 group-hover:scale-110 transition-transform">
                        <iconify-icon icon="solar:widget-5-bold-duotone" width="20"></iconify-icon>
                    </div>
                </div>
            </div>

            <!-- Open -->
            <div class="group relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 min-w-0">
                <div class="flex justify-between items-start gap-2">
                    <div class="min-w-0">
                        <span class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-slate-500 truncate block">{{ __('pages.groups.open') }}</span>
                        <div class="mt-1 sm:mt-2 text-2xl sm:text-3xl font-bold text-slate-900">{{ $globalStats['open'] }}</div>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-50 text-red-600 group-hover:scale-110 transition-transform">
                        <iconify-icon icon="solar:bolt-circle-bold-duotone" width="20"></iconify-icon>
                    </div>
                </div>
            </div>

            <!-- In Progress -->
            <div class="group relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 min-w-0">
                <div class="flex justify-between items-start gap-2">
                    <div class="min-w-0">
                        <span class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-slate-500 truncate block">{{ __('pages.groups.in_progress') }}</span>
                        <div class="mt-1 sm:mt-2 text-2xl sm:text-3xl font-bold text-slate-900">{{ $globalStats['in_progress'] }}</div>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600 group-hover:scale-110 transition-transform">
                        <iconify-icon icon="solar:clock-circle-bold-duotone" width="20"></iconify-icon>
                    </div>
                </div>
            </div>

            <!-- Pending -->
            <div class="group relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 min-w-0">
                <div class="flex justify-between items-start gap-2">
                    <div class="min-w-0">
                        <span class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-slate-500 truncate block">{{ __('pages.groups.pending') }}</span>
                        <div class="mt-1 sm:mt-2 text-2xl sm:text-3xl font-bold text-slate-900">{{ $globalStats['pending'] }}</div>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600 group-hover:scale-110 transition-transform">
                        <iconify-icon icon="solar:hourglass-bold-duotone" width="20"></iconify-icon>
                    </div>
                </div>
            </div>

            <!-- Closed -->
            <div class="col-span-2 sm:col-span-1 group relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 min-w-0">
                <div class="flex justify-between items-start gap-2">
                    <div class="min-w-0">
                        <span class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-slate-500 truncate block">{{ __('pages.groups.closed') }}</span>
                        <div class="mt-1 sm:mt-2 text-2xl sm:text-3xl font-bold text-slate-900">{{ $globalStats['closed'] }}</div>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 group-hover:scale-110 transition-transform">
                        <iconify-icon icon="solar:check-circle-bold-duotone" width="20"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($groups->isEmpty() && !$ungroupedStats)
        <!-- Empty state -->
        <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-12 text-center">
            <div class="mx-auto h-16 w-16 rounded-full bg-slate-50 flex items-center justify-center mb-4">
                <iconify-icon icon="solar:folder-with-files-bold-duotone" width="32" class="text-slate-400"></iconify-icon>
            </div>
            <p class="font-medium text-slate-900">{{ __('pages.groups.no_groups') }}</p>
            <p class="text-sm text-slate-500 mt-1">{{ __('pages.groups.no_groups_hint') }}</p>
            @if($canSeeSettings)
                <a href="{{ route('admin.settings') }}" class="mt-4 inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold text-white shadow-lg hover:opacity-90 transition-all" style="background-color: var(--accent);">
                    <iconify-icon icon="solar:settings-bold-duotone" width="18"></iconify-icon>
                    {{ __('menu.settings') }}
                </a>
            @endif
        </div>
    @else
        <!-- Groups grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            @foreach($groups as $group)
                @php
                    $totalActive = (int) $group->open_count + (int) $group->in_progress_count + (int) $group->pending_count;
                    $color = $group->color ?? 'var(--accent)';
                @endphp
                <a href="{{ route('tickets.index', ['group' => $group->id]) }}"
                   class="group relative rounded-2xl border border-slate-100 bg-white shadow-sm hover:shadow-md hover:border-slate-200 transition-all duration-300 overflow-hidden">
                    <!-- Color accent bar -->
                    <div class="h-1.5 w-full" style="background-color: {{ $color }};"></div>

                    <div class="p-5">
                        <!-- Group name -->
                        <div class="flex items-center gap-3 mb-4">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl text-white text-sm font-bold" style="background-color: {{ $color }};">
                                @if($group->icon)
                                    <iconify-icon icon="{{ $group->icon }}" width="20"></iconify-icon>
                                @else
                                    {{ strtoupper(mb_substr($group->name, 0, 1)) }}
                                @endif
                            </span>
                            <div class="min-w-0">
                                <h3 class="text-sm font-bold text-slate-900 group-hover:text-[var(--accent)] transition-colors truncate">{{ $group->name }}</h3>
                                <span class="text-xs text-slate-500">{{ $totalActive }} {{ __('pages.groups.total_active') }}</span>
                            </div>
                            <iconify-icon icon="solar:alt-arrow-right-linear" width="16" class="ml-auto text-slate-300 group-hover:text-[var(--accent)] group-hover:translate-x-0.5 transition-all shrink-0"></iconify-icon>
                        </div>

                        <!-- Stats -->
                        <div class="grid grid-cols-2 gap-2">
                            <div class="rounded-xl bg-red-50 px-3 py-2">
                                <span class="text-[10px] font-semibold uppercase tracking-wider text-red-600/70">{{ __('pages.groups.open') }}</span>
                                <div class="text-lg font-bold text-red-700">{{ (int) $group->open_count }}</div>
                            </div>
                            <div class="rounded-xl bg-blue-50 px-3 py-2">
                                <span class="text-[10px] font-semibold uppercase tracking-wider text-blue-600/70">{{ __('pages.groups.in_progress') }}</span>
                                <div class="text-lg font-bold text-blue-700">{{ (int) $group->in_progress_count }}</div>
                            </div>
                            <div class="rounded-xl bg-amber-50 px-3 py-2">
                                <span class="text-[10px] font-semibold uppercase tracking-wider text-amber-600/70">{{ __('pages.groups.pending') }}</span>
                                <div class="text-lg font-bold text-amber-700">{{ (int) $group->pending_count }}</div>
                            </div>
                            <div class="rounded-xl bg-emerald-50 px-3 py-2">
                                <span class="text-[10px] font-semibold uppercase tracking-wider text-emerald-600/70">{{ __('pages.groups.closed') }}</span>
                                <div class="text-lg font-bold text-emerald-700">{{ (int) $group->closed_count }}</div>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach

            <!-- Ungrouped card -->
            @if($ungroupedStats)
                @php
                    $ungroupedTotal = (int) $ungroupedStats->open_count + (int) $ungroupedStats->in_progress_count + (int) $ungroupedStats->pending_count;
                @endphp
                <a href="{{ route('tickets.index', ['group' => 'none']) }}"
                   class="group relative rounded-2xl border border-dashed border-slate-200 bg-white shadow-sm hover:shadow-md hover:border-slate-300 transition-all duration-300 overflow-hidden">
                    <!-- Grey accent bar -->
                    <div class="h-1.5 w-full bg-slate-300"></div>

                    <div class="p-5">
                        <!-- Group name -->
                        <div class="flex items-center gap-3 mb-4">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-500 text-sm font-bold">
                                <iconify-icon icon="solar:minus-circle-bold-duotone" width="20"></iconify-icon>
                            </span>
                            <div class="min-w-0">
                                <h3 class="text-sm font-bold text-slate-900 group-hover:text-[var(--accent)] transition-colors truncate">{{ __('pages.groups.ungrouped') }}</h3>
                                <span class="text-xs text-slate-500">{{ $ungroupedTotal }} {{ __('pages.groups.total_active') }}</span>
                            </div>
                            <iconify-icon icon="solar:alt-arrow-right-linear" width="16" class="ml-auto text-slate-300 group-hover:text-[var(--accent)] group-hover:translate-x-0.5 transition-all shrink-0"></iconify-icon>
                        </div>

                        <!-- Stats -->
                        <div class="grid grid-cols-2 gap-2">
                            <div class="rounded-xl bg-red-50 px-3 py-2">
                                <span class="text-[10px] font-semibold uppercase tracking-wider text-red-600/70">{{ __('pages.groups.open') }}</span>
                                <div class="text-lg font-bold text-red-700">{{ (int) $ungroupedStats->open_count }}</div>
                            </div>
                            <div class="rounded-xl bg-blue-50 px-3 py-2">
                                <span class="text-[10px] font-semibold uppercase tracking-wider text-blue-600/70">{{ __('pages.groups.in_progress') }}</span>
                                <div class="text-lg font-bold text-blue-700">{{ (int) $ungroupedStats->in_progress_count }}</div>
                            </div>
                            <div class="rounded-xl bg-amber-50 px-3 py-2">
                                <span class="text-[10px] font-semibold uppercase tracking-wider text-amber-600/70">{{ __('pages.groups.pending') }}</span>
                                <div class="text-lg font-bold text-amber-700">{{ (int) $ungroupedStats->pending_count }}</div>
                            </div>
                            <div class="rounded-xl bg-emerald-50 px-3 py-2">
                                <span class="text-[10px] font-semibold uppercase tracking-wider text-emerald-600/70">{{ __('pages.groups.closed') }}</span>
                                <div class="text-lg font-bold text-emerald-700">{{ (int) $ungroupedStats->closed_count }}</div>
                            </div>
                        </div>
                    </div>
                </a>
            @endif
        </div>
    @endif
</div>
