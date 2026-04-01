@php
    $volumeSeries = $performanceSeries ?? [];
    $isYearly = ($period ?? 'default') === 'yearly';
    $maxVolume = max(1, collect($volumeSeries)->max('count') ?? 1);
    $trendTotal = $trendTotal ?? ['dir' => 'up', 'val' => 0];
    $trendNew30d = $trendNew30d ?? ['dir' => 'up', 'val' => 0];
    $resolutionRate = $resolutionRate ?? 0;
    $teamCapacityPercent = $teamCapacityPercent ?? 0;
    $performanceSeries = $performanceSeries ?? [];
    $perfByDate = collect($performanceSeries)->keyBy('date');
    $perfMax = max(1, collect($performanceSeries)->max('count') ?? 1);
    $perfCount = count($performanceSeries);
    $reportPeriodChartOptions = [
        ['value' => 'default', 'label' => __('reports.view_default')],
        ['value' => 'monthly', 'label' => __('reports.this_month')],
        ['value' => 'yearly', 'label' => __('reports.this_year')],
    ];
    $reportPeriodChartLabel = collect($reportPeriodChartOptions)->firstWhere('value', $period ?? 'default')['label'] ?? ($period ?? 'default');
    $topCategoriesTotal = max(1, array_sum(array_column($topCategories ?? [], 'count')));
    $volumePointCount = count($volumeSeries);
    $needsWideVolumeChart = in_array(($period ?? 'default'), ['monthly', 'yearly'], true);
    $volumeChartMinWidth = $needsWideVolumeChart ? max(1200, $volumePointCount * 52) : 0;
@endphp

<div class="w-full max-w-full min-w-0 mx-auto" wire:init="loadReportBody">
    {{-- ═══ HEADER ═══ --}}
    <div class="page-header">
        <div class="min-w-0">
            <h1 class="page-title">{{ __('reports.title') }}</h1>
            <p class="page-subtitle">{{ __('reports.subtitle') }}</p>
        </div>
        <div class="page-actions">
            <div class="view-toggle flex-shrink-0">
                <button wire:click="setPeriod('default')" wire:loading.attr="disabled" wire:target="setPeriod,period" type="button" class="view-toggle-btn whitespace-nowrap {{ ($period ?? 'default') === 'default' ? 'view-toggle-btn-active' : 'view-toggle-btn-default' }}">{{ __('reports.view_default') }}</button>
                <button wire:click="setPeriod('monthly')" wire:loading.attr="disabled" wire:target="setPeriod,period" type="button" class="view-toggle-btn whitespace-nowrap {{ ($period ?? '') === 'monthly' ? 'view-toggle-btn-active' : 'view-toggle-btn-default' }}">{{ __('reports.monthly') }}</button>
                <button wire:click="setPeriod('yearly')" wire:loading.attr="disabled" wire:target="setPeriod,period" type="button" class="view-toggle-btn whitespace-nowrap {{ ($period ?? '') === 'yearly' ? 'view-toggle-btn-active' : 'view-toggle-btn-default' }}">{{ __('reports.yearly') }}</button>
            </div>
            <x-dropdown align="right" width="56" contentClasses="py-1 bg-white rounded-xl shadow-xl border border-slate-200">
                <x-slot name="trigger">
                    <button type="button" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2.5 sm:px-4 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-all">
                        <iconify-icon icon="solar:filter-linear" width="18"></iconify-icon>
                        {{ __('reports.filters') }}
                    </button>
                </x-slot>
                <x-slot name="content">
                    <p class="px-4 py-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('reports.menu_chart_period') }}</p>
                    <button wire:click="setPeriod('default')" wire:loading.attr="disabled" wire:target="setPeriod,period" type="button" class="block w-full px-4 py-2 text-start text-sm text-slate-700 hover:bg-slate-100 transition-colors flex items-center gap-2 rounded-t {{ ($period ?? 'default') === 'default' ? 'bg-slate-100 font-medium text-slate-900' : '' }}">
                        {{ __('reports.view_default') }}
                    </button>
                    <button wire:click="setPeriod('monthly')" wire:loading.attr="disabled" wire:target="setPeriod,period" type="button" class="block w-full px-4 py-2 text-start text-sm text-slate-700 hover:bg-slate-100 transition-colors flex items-center gap-2 {{ ($period ?? '') === 'monthly' ? 'bg-slate-100 font-medium text-slate-900' : '' }}">
                        {{ __('reports.this_month') }}
                    </button>
                    <button wire:click="setPeriod('yearly')" wire:loading.attr="disabled" wire:target="setPeriod,period" type="button" class="block w-full px-4 py-2 text-start text-sm text-slate-700 hover:bg-slate-100 transition-colors rounded-b-lg flex items-center gap-2 {{ ($period ?? '') === 'yearly' ? 'bg-slate-100 font-medium text-slate-900' : '' }}">
                        {{ __('reports.this_year') }}
                    </button>
                </x-slot>
            </x-dropdown>
        </div>
    </div>

    @if($loadStage >= 2)
    <!-- TOP KPI CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Card 1: Total Users (Blue Highlight) -->
        <div class="relative overflow-hidden rounded-xl sm:rounded-2xl bg-[var(--accent)] p-6 text-white shadow-lg shadow-[var(--accent-ring)] group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <iconify-icon icon="solar:users-group-rounded-bold" width="80"></iconify-icon>
            </div>
            <div class="relative z-10 flex flex-col justify-between h-full">
                <div class="flex justify-between items-start">
                    <p class="text-sm font-medium text-white/80">{{ __('reports.total_tickets') }}</p>
                    <x-dropdown align="right" width="48" contentClasses="py-1 bg-white rounded-lg shadow-xl border border-slate-200">
                        <x-slot name="trigger">
                            <button type="button" class="text-white/60 hover:text-white p-1 rounded-lg hover:bg-white/10 transition-colors" aria-label="{{ __('reports.filters') }}">
                                <iconify-icon icon="solar:menu-dots-bold" width="20"></iconify-icon>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <a href="{{ route('tickets.index') }}" class="block w-full px-4 py-2 text-start text-sm text-slate-700 hover:bg-slate-50 transition-colors rounded-t-lg flex items-center gap-2">
                                <iconify-icon icon="solar:document-text-bold-duotone" width="16"></iconify-icon>
                                {{ __('reports.menu_view_tickets') }}
                            </a>
                            <a href="{{ route('reports.index') }}" class="block w-full px-4 py-2 text-start text-sm text-slate-700 hover:bg-slate-50 transition-colors rounded-b-lg flex items-center gap-2">
                                <iconify-icon icon="solar:refresh-bold-duotone" width="16"></iconify-icon>
                                {{ __('reports.menu_refresh') }}
                            </a>
                        </x-slot>
                    </x-dropdown>
                </div>
                <div class="mt-4">
                    <h3 class="text-3xl font-bold">{{ number_format($kpis['total'] ?? 0, 0, ',', ' ') }}</h3>
                    <div class="flex items-center gap-2 mt-2">
                        @if(($trendTotal['val'] ?? 0) > 0)
                        <span class="inline-flex items-center gap-1 rounded-full bg-white/20 px-2 py-0.5 text-xs font-medium text-white backdrop-blur-sm">
                            @if($trendTotal['dir'] === 'up')
                                <iconify-icon icon="solar:arrow-right-up-linear" width="12"></iconify-icon>
                            @else
                                <iconify-icon icon="solar:arrow-right-down-linear" width="12"></iconify-icon>
                            @endif
                            {{ $trendTotal['dir'] === 'up' ? '+' : '-' }}{{ $trendTotal['val'] }}%
                        </span>
                        @endif
                        <span class="text-xs text-white/60">{{ __('reports.vs_last_month') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: New Tickets (period-aware) -->
        @php
            $periodLabel = match($period ?? 'default') {
                'monthly' => __('reports.new_month'),
                'yearly' => __('reports.new_year'),
                default => __('reports.new_30d'),
            };
            $newCount = (int) ($createdInPeriod ?? 0);
            $newTrend = ($period ?? 'default') === 'default' ? ($trendNew30d ?? ['dir' => 'up', 'val' => 0]) : ($trendNewInPeriod ?? ['dir' => 'up', 'val' => 0]);
        @endphp
        <div class="stat-card !p-6 !rounded-2xl">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">{{ $periodLabel }}</p>
                    <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $newCount }}</h3>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                    <iconify-icon icon="solar:add-circle-bold-duotone" width="24"></iconify-icon>
                </div>
            </div>
            <div class="flex items-center gap-2">
                @if(($newTrend['val'] ?? 0) > 0)
                <span class="text-xs font-medium {{ ($newTrend['dir'] ?? 'up') === 'up' ? 'text-emerald-600' : 'text-red-600' }} flex items-center">
                    @if(($newTrend['dir'] ?? 'up') === 'up')
                        <iconify-icon icon="solar:arrow-right-up-linear" width="12"></iconify-icon>
                    @else
                        <iconify-icon icon="solar:arrow-right-down-linear" width="12"></iconify-icon>
                    @endif
                    {{ ($newTrend['dir'] ?? 'up') === 'up' ? '+' : '-' }}{{ $newTrend['val'] ?? 0 }}%
                </span>
                @endif
                <span class="text-xs text-slate-400">{{ __('reports.vs_prev_period') }}</span>
            </div>
        </div>

        <!-- Card 3: Avg Time -->
        <div class="stat-card !p-6 !rounded-2xl">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">{{ __('reports.response_time') }}</p>
                    <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $kpis['avg_open_age_hours'] }}<span class="text-sm text-slate-400 font-normal ml-1">h</span></h3>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                    <iconify-icon icon="solar:clock-circle-bold-duotone" width="24"></iconify-icon>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-400">{{ __('reports.response_time') }}</span>
            </div>
        </div>

        <!-- Card 4: Active Now -->
        <div class="stat-card !p-6 !rounded-2xl">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">{{ __('reports.in_progress') }}</p>
                    <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $kpis['open'] + $kpis['in_progress'] }}</h3>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                    <iconify-icon icon="solar:bolt-circle-bold-duotone" width="24"></iconify-icon>
                </div>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-1.5 mt-2 overflow-hidden">
                <div class="bg-blue-500 h-1.5 rounded-full" style="width: {{ min(100, $teamCapacityPercent) }}%"></div>
            </div>
            <p class="text-xs text-slate-400 mt-2">{{ __('reports.team_capacity', ['percent' => $teamCapacityPercent]) }}</p>
        </div>
    </div>

    <!-- MAIN CHARTS SECTION -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

        <!-- Large Bar Chart (Statistics) -->
        <div class="lg:col-span-2 content-card p-6">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-lg font-bold text-slate-900">{{ __('reports.ticket_volume') }}</h3>
                <div class="flex items-center gap-2">
                    <span class="flex items-center gap-1.5 text-xs font-medium text-slate-600">
                        <span class="w-2.5 h-2.5 rounded-full bg-[var(--accent)]"></span> {{ __('reports.created') }}
                    </span>
                    <span class="flex items-center gap-1.5 text-xs font-medium text-slate-600">
                        <span class="w-2.5 h-2.5 rounded-full bg-slate-200"></span> {{ __('reports.resolved') }}
                    </span>
                    <div class="h-4 w-px bg-slate-200 mx-2"></div>
                    <x-select-input
                        :options="$reportPeriodChartOptions"
                        :label="$reportPeriodChartLabel"
                        :selected-value="$period ?? 'default'"
                        wire:model="period"
                        wire:loading.attr="disabled"
                        wire:target="setPeriod,period"
                    />
                </div>
            </div>

            <!-- CSS Bar Chart (period-aware: default 14d, monthly by day, yearly by month) -->
            <div class="relative w-full">
                <div wire:loading.flex wire:target="setPeriod,period" class="absolute inset-0 z-10 items-center justify-center bg-white/60 backdrop-blur-[1px] text-xs text-slate-500">
                    {{ __('reports.menu_refresh') }}...
                </div>
                <div class="overflow-x-auto overflow-y-hidden pb-2 [touch-action:pan-x]">
                    <div class="w-full" @if($needsWideVolumeChart) style="width: {{ $volumeChartMinWidth }}px;" @endif>
                        @if (empty($volumeSeries))
                            <div class="h-64 flex items-center justify-center text-sm text-slate-400">{{ __('reports.no_data') }}</div>
                        @else
                            @php $step = $isYearly ? 1 : (count($volumeSeries) > 20 ? 2 : 1); @endphp
                            <div class="flex items-end justify-between h-64 gap-2 sm:gap-3 min-w-max">
                                @foreach ($volumeSeries as $index => $p)
                                    @if($index % $step === 0)
                                        @php
                                            $h = max(4, (int) round(((int) ($p['count'] ?? 0) / $maxVolume) * 100));
                                            $isMax = $h > 80;
                                            $dateLabel = $isYearly ? \Illuminate\Support\Carbon::createFromFormat('Y-m', $p['date'])->translatedFormat('M Y') : \Illuminate\Support\Carbon::parse($p['date'])->format('d M');
                                        @endphp
                                        <div class="flex-1 flex flex-col justify-end group h-full relative min-w-[18px]">
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block z-10">
                                                <div class="bg-slate-900 text-white text-[10px] py-1 px-2 rounded shadow-lg whitespace-nowrap">
                                                    {{ $p['count'] ?? 0 }} {{ __('reports.tickets') }}<br>
                                                    <span class="text-slate-400">{{ $dateLabel }}</span>
                                                </div>
                                            </div>
                                            <div class="w-full rounded-t-md transition-all duration-300 relative {{ $isMax ? 'bg-[var(--accent)]' : 'bg-slate-100 hover:bg-slate-200' }}"
                                                 style="height: {{ $h }}%;">
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            @php
                                $first = $volumeSeries[0]['date'] ?? null;
                                $mid = $volumeSeries[(int) floor(count($volumeSeries) / 2)]['date'] ?? null;
                                $last = $volumeSeries[count($volumeSeries) - 1]['date'] ?? null;
                                $fmt = $isYearly ? fn($d) => \Illuminate\Support\Carbon::createFromFormat('Y-m', $d)->translatedFormat('M y') : fn($d) => \Illuminate\Support\Carbon::parse($d)->format('d M');
                            @endphp
                            <div class="flex justify-between mt-4 text-[10px] text-slate-400 uppercase font-medium tracking-wider">
                                <span>{{ $first ? $fmt($first) : '—' }}</span>
                                <span>{{ $mid ? $fmt($mid) : '—' }}</span>
                                <span>{{ $last ? $fmt($last) : '—' }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Secondary Chart (Performance/Financial style) -->
        <div class="content-card p-6 flex flex-col">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-slate-900">{{ __('reports.performance') }}</h3>
                <x-dropdown align="right" width="48" contentClasses="py-1 bg-white rounded-lg shadow-xl border border-slate-200">
                    <x-slot name="trigger">
                        <button type="button" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors" aria-label="{{ __('reports.menu_settings') }}">
                            <iconify-icon icon="solar:settings-linear" width="20"></iconify-icon>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <a href="{{ route('admin.settings') }}" class="block w-full px-4 py-2 text-start text-sm text-slate-700 hover:bg-slate-50 transition-colors rounded-t-lg flex items-center gap-2">
                            <iconify-icon icon="solar:settings-bold-duotone" width="16"></iconify-icon>
                            {{ __('reports.menu_settings') }}
                        </a>
                        <a href="{{ route('reports.index') }}" class="block w-full px-4 py-2 text-start text-sm text-slate-700 hover:bg-slate-50 transition-colors rounded-b-lg flex items-center gap-2">
                            <iconify-icon icon="solar:refresh-bold-duotone" width="16"></iconify-icon>
                            {{ __('reports.menu_refresh') }}
                        </a>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Line Chart: period-aware (default 14d, monthly by day, yearly by month) -->
            <div class="relative h-40 w-full mb-6">
                @php
                    $perfList = $performanceSeries ?? [];
                    $n = count($perfList);
                    $points = [];
                    if ($n > 0) {
                        $xStep = $n > 1 ? 100 / ($n - 1) : 100;
                        foreach ($perfList as $i => $item) {
                            $count = (int) ($item['count'] ?? 0);
                            $y = $perfMax > 0 ? 38 - round(($count / $perfMax) * 35) : 38;
                            $x = $i * $xStep;
                            $points[] = round($x, 1) . ',' . max(3, min(38, (int) $y));
                        }
                    }
                    $pathD = count($points) > 0 ? ('M' . implode(' L', $points)) : 'M0,38 L100,38';
                    $areaD = $pathD . ' L100,40 L0,40 Z';
                    $lastPoint = $points[count($points) - 1] ?? '0,38';
                @endphp
                <svg viewBox="0 0 100 40" class="w-full h-full overflow-visible" preserveAspectRatio="none">
                    <line x1="0" y1="0" x2="100" y2="0" stroke="#f1f5f9" stroke-width="0.5" />
                    <line x1="0" y1="10" x2="100" y2="10" stroke="#f1f5f9" stroke-width="0.5" />
                    <line x1="0" y1="20" x2="100" y2="20" stroke="#f1f5f9" stroke-width="0.5" />
                    <line x1="0" y1="30" x2="100" y2="30" stroke="#f1f5f9" stroke-width="0.5" />
                    <path d="{{ $areaD }}" fill="var(--accent)" fill-opacity="0.1" stroke="none" />
                    <path d="{{ $pathD }}" fill="none" stroke="var(--accent)" stroke-width="2" vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" />
                    @if(count($points) > 0)
                        <circle cx="{{ explode(',', $lastPoint)[0] }}" cy="{{ explode(',', $lastPoint)[1] }}" r="3" fill="white" stroke="var(--accent)" stroke-width="2" />
                    @endif
                </svg>
            </div>

            <div class="mt-auto space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-slate-500">{{ __('reports.resolution_rate') }}</p>
                        <p class="text-lg font-bold text-slate-900">{{ $resolutionRate }}%</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-medium text-slate-500">{{ __('reports.satisfaction') }}</p>
                        <p class="text-lg font-bold text-slate-900">—</p>
                    </div>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2">
                    <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ min(100, $resolutionRate) }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- BOTTOM DETAILED TABLE -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Agents -->
        <div class="content-card">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900">{{ __('reports.agent_performance') }}</h3>
                <x-dropdown align="right" width="48" contentClasses="py-1 bg-white rounded-lg shadow-xl border border-slate-200">
                    <x-slot name="trigger">
                        <button type="button" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition-colors" aria-label="{{ __('reports.filters') }}">
                            <iconify-icon icon="solar:menu-dots-bold" width="20"></iconify-icon>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <a href="{{ route('admin.users') }}" class="block w-full px-4 py-2 text-start text-sm text-slate-700 hover:bg-slate-50 transition-colors rounded-t-lg flex items-center gap-2">
                            <iconify-icon icon="solar:users-group-rounded-bold-duotone" width="16"></iconify-icon>
                            {{ __('reports.menu_view_team') }}
                        </a>
                        <a href="{{ route('reports.index') }}" class="block w-full px-4 py-2 text-start text-sm text-slate-700 hover:bg-slate-50 transition-colors rounded-b-lg flex items-center gap-2">
                            <iconify-icon icon="solar:refresh-bold-duotone" width="16"></iconify-icon>
                            {{ __('reports.menu_refresh') }}
                        </a>
                    </x-slot>
                </x-dropdown>
            </div>
            <div class="p-0">
                <table class="w-full">
                    <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500">
                        <tr>
                            <th class="px-6 py-3 text-left">{{ __('reports.agent') }}</th>
                            <th class="px-6 py-3 text-left">{{ __('reports.volume') }}</th>
                            <th class="px-6 py-3 text-right">{{ __('reports.efficiency') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($topAssignees as $agent)
                            @php $efficiency = (int) ($agent['efficiency'] ?? 0); @endphp
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="h-8 w-8 rounded-full bg-slate-100 text-slate-700 inline-flex items-center justify-center text-xs font-semibold">
                                            {{ \Illuminate\Support\Str::of($agent['name'] ?? 'U')->explode(' ')->take(2)->map(fn ($p) => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($p, 0, 1)))->implode('') }}
                                        </span>
                                        <span class="text-sm font-medium text-slate-900">{{ $agent['name'] }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-semibold text-slate-700">{{ $agent['count'] }} {{ __('reports.tickets') }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-3">
                                        <span class="text-xs font-medium text-slate-600">{{ $efficiency }}%</span>
                                        <div class="w-16 bg-slate-100 rounded-full h-1.5">
                                            <div class="bg-[var(--accent)] h-1.5 rounded-full" style="width: {{ min(100, $efficiency) }}%"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-6 py-8 text-center text-sm text-slate-500">{{ __('reports.no_data') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Categories Breakdown -->
        <div class="content-card">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900">{{ __('reports.category_breakdown') }}</h3>
                <x-dropdown align="right" width="48" contentClasses="py-1 bg-white rounded-lg shadow-xl border border-slate-200">
                    <x-slot name="trigger">
                        <button type="button" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition-colors" aria-label="{{ __('reports.filters') }}">
                            <iconify-icon icon="solar:menu-dots-bold" width="20"></iconify-icon>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <a href="{{ route('tickets.index') }}" class="block w-full px-4 py-2 text-start text-sm text-slate-700 hover:bg-slate-50 transition-colors rounded-t-lg flex items-center gap-2">
                            <iconify-icon icon="solar:document-text-bold-duotone" width="16"></iconify-icon>
                            {{ __('reports.menu_view_tickets') }}
                        </a>
                        <a href="{{ route('reports.index') }}" class="block w-full px-4 py-2 text-start text-sm text-slate-700 hover:bg-slate-50 transition-colors rounded-b-lg flex items-center gap-2">
                            <iconify-icon icon="solar:refresh-bold-duotone" width="16"></iconify-icon>
                            {{ __('reports.menu_refresh') }}
                        </a>
                    </x-slot>
                </x-dropdown>
            </div>
            <div class="p-6 space-y-5">
                @forelse($topCategories as $cat)
                    @php $pct = round(($cat['count'] / $topCategoriesTotal) * 100); @endphp
                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <span class="text-sm font-medium text-slate-700">{{ $cat['name'] }}</span>
                            <span class="text-xs font-bold text-slate-900">{{ $cat['count'] }} <span class="text-slate-400 font-normal">({{ $pct }}%)</span></span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2">
                            <div class="bg-slate-800 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-sm text-slate-500 py-8">{{ __('reports.no_data') }}</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- SLA Conformity Section --}}
    @if($slaKpis ?? null)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mt-8">
            <!-- SLA First Response Rate -->
            <div class="stat-card !p-6 !rounded-2xl">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-sm font-medium text-slate-500">{{ __('SLA Première réponse') }}</p>
                        <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $slaKpis['fr_met_pct'] }}<span class="text-sm text-slate-400 font-normal ml-1">%</span></h3>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                        <iconify-icon icon="solar:check-circle-bold-duotone" width="24"></iconify-icon>
                    </div>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2">
                    <div class="h-2 rounded-full {{ $slaKpis['fr_met_pct'] >= 80 ? 'bg-emerald-500' : ($slaKpis['fr_met_pct'] >= 50 ? 'bg-amber-500' : 'bg-red-500') }}" style="width: {{ min(100, $slaKpis['fr_met_pct']) }}%"></div>
                </div>
                <p class="text-xs text-slate-400 mt-2">{{ __('Taux de respect') }} · {{ $slaKpis['fr_breached_pct'] }}% {{ __('en breach') }}</p>
            </div>

            <!-- SLA Resolution Rate -->
            <div class="stat-card !p-6 !rounded-2xl">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-sm font-medium text-slate-500">{{ __('SLA Résolution') }}</p>
                        <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $slaKpis['res_met_pct'] }}<span class="text-sm text-slate-400 font-normal ml-1">%</span></h3>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <iconify-icon icon="solar:clock-circle-bold-duotone" width="24"></iconify-icon>
                    </div>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2">
                    <div class="h-2 rounded-full {{ $slaKpis['res_met_pct'] >= 80 ? 'bg-emerald-500' : ($slaKpis['res_met_pct'] >= 50 ? 'bg-amber-500' : 'bg-red-500') }}" style="width: {{ min(100, $slaKpis['res_met_pct']) }}%"></div>
                </div>
                <p class="text-xs text-slate-400 mt-2">{{ __('Taux de respect') }} · {{ $slaKpis['res_breached_pct'] }}% {{ __('en breach') }}</p>
            </div>

            <!-- SLA Total Tracked -->
            <div class="stat-card !p-6 !rounded-2xl">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-sm font-medium text-slate-500">{{ __('Tickets suivis SLA') }}</p>
                        <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $slaKpis['total'] }}</h3>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                        <iconify-icon icon="solar:alarm-bold-duotone" width="24"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>

        {{-- SLA by Priority Table --}}
        @if(!empty($slaKpis['by_priority']))
            <div class="content-card mt-6">
                <div class="px-6 py-5 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-900">{{ __('Conformité SLA par priorité') }}</h3>
                </div>
                <div class="p-0">
                    <table class="w-full">
                        <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500">
                            <tr>
                                <th class="px-6 py-3 text-left">{{ __('Priorité') }}</th>
                                <th class="px-6 py-3 text-left">{{ __('Tickets') }}</th>
                                <th class="px-6 py-3 text-right">{{ __('PR respecté') }}</th>
                                <th class="px-6 py-3 text-right">{{ __('RES respecté') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($slaKpis['by_priority'] as $sp)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4 text-sm font-medium text-slate-900">{{ $sp['name'] }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-700">{{ $sp['total'] }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="text-sm font-semibold {{ $sp['fr_met_pct'] >= 80 ? 'text-emerald-600' : ($sp['fr_met_pct'] >= 50 ? 'text-amber-600' : 'text-red-600') }}">{{ $sp['fr_met_pct'] }}%</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="text-sm font-semibold {{ $sp['res_met_pct'] >= 80 ? 'text-emerald-600' : ($sp['res_met_pct'] >= 50 ? 'text-amber-600' : 'text-red-600') }}">{{ $sp['res_met_pct'] }}%</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endif

    @else
        <x-page-skeleton variant="list" />
    @endif

</div>
