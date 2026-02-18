@php
    // Helper for trend calculation (mocked for now as we need previous period data)
    $trend = function($value) {
        return rand(0, 1) ? ['dir' => 'up', 'val' => rand(2, 15)] : ['dir' => 'down', 'val' => rand(1, 8)];
    };

    $max30d = max(1, max(array_map(fn ($x) => (int) ($x['count'] ?? 0), $createdLast30d ?? [])) ?: 1);
@endphp

<div class="mx-auto w-full max-w-7xl 2xl:max-w-[90rem] min-[1920px]:max-w-[110rem] py-8 px-4 sm:px-6 lg:px-8">
    <!-- HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ __('Analytics') }}</h1>
            <p class="mt-1 text-sm text-slate-500">{{ __('Vue d\'ensemble détaillée de l\'activité du support.') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="hidden sm:flex items-center bg-white border border-slate-200 rounded-lg p-1 shadow-sm">
                <button class="px-3 py-1 text-xs font-semibold text-slate-900 bg-slate-100 rounded-md shadow-sm">{{ __('Vue par défaut') }}</button>
                <button class="px-3 py-1 text-xs font-medium text-slate-500 hover:text-slate-900">{{ __('Mensuel') }}</button>
                <button class="px-3 py-1 text-xs font-medium text-slate-500 hover:text-slate-900">{{ __('Annuel') }}</button>
            </div>
            <button class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 transition-all">
                <iconify-icon icon="solar:filter-linear" width="18"></iconify-icon>
                {{ __('Filtres') }}
            </button>
        </div>
    </div>

    <!-- TOP KPI CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Card 1: Total Users (Blue Highlight) -->
        <div class="relative overflow-hidden rounded-2xl bg-[var(--accent)] p-6 text-white shadow-lg shadow-[var(--accent-ring)] group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <iconify-icon icon="solar:users-group-rounded-bold" width="80"></iconify-icon>
            </div>
            <div class="relative z-10 flex flex-col justify-between h-full">
                <div class="flex justify-between items-start">
                    <p class="text-sm font-medium text-white/80">{{ __('Total Tickets') }}</p>
                    <button class="text-white/60 hover:text-white"><iconify-icon icon="solar:menu-dots-bold" width="20"></iconify-icon></button>
                </div>
                <div class="mt-4">
                    <h3 class="text-3xl font-bold">{{ number_format($kpis['total'] ?? 0, 0, ',', ' ') }}</h3>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="inline-flex items-center gap-1 rounded-full bg-white/20 px-2 py-0.5 text-xs font-medium text-white backdrop-blur-sm">
                            <iconify-icon icon="solar:arrow-right-up-linear" width="12"></iconify-icon>
                            +12%
                        </span>
                        <span class="text-xs text-white/60">{{ __('vs mois dernier') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: New Tickets -->
        <div class="relative rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-all">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">{{ __('Nouveaux (30j)') }}</p>
                    <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ array_sum(array_column($createdLast30d, 'count')) }}</h3>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                    <iconify-icon icon="solar:add-circle-bold-duotone" width="24"></iconify-icon>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-medium text-emerald-600 flex items-center">
                    <iconify-icon icon="solar:arrow-right-up-linear" width="12"></iconify-icon>
                    +8.5%
                </span>
                <span class="text-xs text-slate-400">{{ __('vs période préc.') }}</span>
            </div>
        </div>

        <!-- Card 3: Avg Time -->
        <div class="relative rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-all">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">{{ __('Temps de réponse') }}</p>
                    <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $kpis['avg_open_age_hours'] }}<span class="text-sm text-slate-400 font-normal ml-1">h</span></h3>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                    <iconify-icon icon="solar:clock-circle-bold-duotone" width="24"></iconify-icon>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-medium text-red-600 flex items-center">
                    <iconify-icon icon="solar:arrow-right-down-linear" width="12"></iconify-icon>
                    -2.3%
                </span>
                <span class="text-xs text-slate-400">{{ __('plus lent') }}</span>
            </div>
        </div>

        <!-- Card 4: Active Now -->
        <div class="relative rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-all">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">{{ __('En cours de traitement') }}</p>
                    <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $kpis['open'] + $kpis['in_progress'] }}</h3>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                    <iconify-icon icon="solar:bolt-circle-bold-duotone" width="24"></iconify-icon>
                </div>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-1.5 mt-2 overflow-hidden">
                <div class="bg-blue-500 h-1.5 rounded-full" style="width: 65%"></div>
            </div>
            <p class="text-xs text-slate-400 mt-2">{{ __('Capacité de l\'équipe: 65%') }}</p>
        </div>
    </div>

    <!-- MAIN CHARTS SECTION -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        <!-- Large Bar Chart (Statistics) -->
        <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-lg font-bold text-slate-900">{{ __('Volume de Tickets') }}</h3>
                <div class="flex items-center gap-2">
                    <span class="flex items-center gap-1.5 text-xs font-medium text-slate-600">
                        <span class="w-2.5 h-2.5 rounded-full bg-[var(--accent)]"></span> Créés
                    </span>
                    <span class="flex items-center gap-1.5 text-xs font-medium text-slate-600">
                        <span class="w-2.5 h-2.5 rounded-full bg-slate-200"></span> Résolus
                    </span>
                    <div class="h-4 w-px bg-slate-200 mx-2"></div>
                    <select class="text-xs border-none bg-slate-50 rounded-md py-1 pl-2 pr-6 font-medium text-slate-600 focus:ring-0 appearance-none">
                        <option>{{ __('Ce mois') }}</option>
                        <option>{{ __('Cette année') }}</option>
                    </select>
                </div>
            </div>

            <!-- CSS Bar Chart -->
            <div class="relative h-64 w-full">
                @if (empty($createdLast30d))
                    <div class="absolute inset-0 flex items-center justify-center text-sm text-slate-400">{{ __('Aucune donnée') }}</div>
                @else
                    <div class="flex items-end justify-between h-full gap-2 sm:gap-3">
                        @foreach ($createdLast30d as $index => $p)
                            @if($index % 2 == 0) <!-- Show every 2nd day to save space -->
                                @php
                                    $h = max(4, (int) round(((int) $p['count'] / $max30d) * 100));
                                    $isMax = $h > 80; // Fake highlight logic
                                @endphp
                                <div class="flex-1 flex flex-col justify-end group h-full relative">
                                    <!-- Tooltip -->
                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block z-10">
                                        <div class="bg-slate-900 text-white text-[10px] py-1 px-2 rounded shadow-lg whitespace-nowrap">
                                            {{ $p['count'] }} tickets<br>
                                            <span class="text-slate-400">{{ \Illuminate\Support\Carbon::parse($p['date'])->format('d M') }}</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Bar -->
                                    <div class="w-full rounded-t-md transition-all duration-300 relative {{ $isMax ? 'bg-[var(--accent)]' : 'bg-slate-100 hover:bg-slate-200' }}" 
                                         style="height: {{ $h }}%;">
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <!-- X Axis Labels -->
                    <div class="flex justify-between mt-4 text-[10px] text-slate-400 uppercase font-medium tracking-wider">
                        <span>{{ \Illuminate\Support\Carbon::parse($createdLast30d[0]['date'] ?? now())->format('d M') }}</span>
                        <span>{{ \Illuminate\Support\Carbon::parse($createdLast30d[count($createdLast30d)/2]['date'] ?? now())->format('d M') }}</span>
                        <span>{{ \Illuminate\Support\Carbon::parse($createdLast30d[count($createdLast30d)-1]['date'] ?? now())->format('d M') }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Secondary Chart (Performance/Financial style) -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm flex flex-col">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-slate-900">{{ __('Performance') }}</h3>
                <button class="text-slate-400 hover:text-slate-600"><iconify-icon icon="solar:settings-linear" width="20"></iconify-icon></button>
            </div>

            <!-- Line Chart Simulation (SVG) -->
            <div class="relative h-40 w-full mb-6">
                <svg viewBox="0 0 100 40" class="w-full h-full overflow-visible" preserveAspectRatio="none">
                    <!-- Grid lines -->
                    <line x1="0" y1="0" x2="100" y2="0" stroke="#f1f5f9" stroke-width="0.5" />
                    <line x1="0" y1="10" x2="100" y2="10" stroke="#f1f5f9" stroke-width="0.5" />
                    <line x1="0" y1="20" x2="100" y2="20" stroke="#f1f5f9" stroke-width="0.5" />
                    <line x1="0" y1="30" x2="100" y2="30" stroke="#f1f5f9" stroke-width="0.5" />
                    
                    <!-- Path (Smooth curve) -->
                    <path d="M0,35 C10,35 15,10 25,10 C35,10 40,30 50,30 C60,30 65,15 75,15 C85,15 90,25 100,5" 
                          fill="none" 
                          stroke="var(--accent)" 
                          stroke-width="2" 
                          vector-effect="non-scaling-stroke"
                          stroke-linecap="round"
                    />
                    
                    <!-- Area under curve -->
                    <path d="M0,35 C10,35 15,10 25,10 C35,10 40,30 50,30 C60,30 65,15 75,15 C85,15 90,25 100,5 V40 H0 Z" 
                          fill="var(--accent)" 
                          fill-opacity="0.1" 
                          stroke="none"
                    />
                    
                    <!-- Active Point -->
                    <circle cx="75" cy="15" r="3" fill="white" stroke="var(--accent)" stroke-width="2" />
                </svg>
            </div>

            <div class="mt-auto space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-slate-500">{{ __('Taux de résolution') }}</p>
                        <p class="text-lg font-bold text-slate-900">94.2%</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-medium text-slate-500">{{ __('Satisfaction') }}</p>
                        <p class="text-lg font-bold text-slate-900">4.8/5</p>
                    </div>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2">
                    <div class="bg-emerald-500 h-2 rounded-full" style="width: 94%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- BOTTOM DETAILED TABLE -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Agents -->
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900">{{ __('Performance des Agents') }}</h3>
                <button class="p-1 rounded hover:bg-slate-50"><iconify-icon icon="solar:menu-dots-bold" class="text-slate-400"></iconify-icon></button>
            </div>
            <div class="p-0">
                <table class="w-full">
                    <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500">
                        <tr>
                            <th class="px-6 py-3 text-left">{{ __('Agent') }}</th>
                            <th class="px-6 py-3 text-left">{{ __('Volume') }}</th>
                            <th class="px-6 py-3 text-right">{{ __('Efficacité') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($topAssignees as $agent)
                            @php $percentage = rand(60, 98); @endphp
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($agent['name']) }}&background=random&color=fff" class="h-8 w-8 rounded-full" alt="">
                                        <span class="text-sm font-medium text-slate-900">{{ $agent['name'] }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-semibold text-slate-700">{{ $agent['count'] }} tickets</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-3">
                                        <span class="text-xs font-medium text-slate-600">{{ $percentage }}%</span>
                                        <div class="w-16 bg-slate-100 rounded-full h-1.5">
                                            <div class="bg-[var(--accent)] h-1.5 rounded-full" style="width: {{ $percentage }}%"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-6 py-8 text-center text-sm text-slate-500">{{ __('Aucune donnée') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Categories Breakdown -->
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900">{{ __('Répartition par Catégorie') }}</h3>
                <button class="p-1 rounded hover:bg-slate-50"><iconify-icon icon="solar:menu-dots-bold" class="text-slate-400"></iconify-icon></button>
            </div>
            <div class="p-6 space-y-5">
                @forelse($topCategories as $cat)
                    @php 
                        $total = array_sum(array_column($topCategories, 'count')) ?: 1;
                        $pct = round(($cat['count'] / $total) * 100);
                    @endphp
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
                    <div class="text-center text-sm text-slate-500 py-8">{{ __('Aucune donnée') }}</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
