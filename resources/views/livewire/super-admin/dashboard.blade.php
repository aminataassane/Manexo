<div class="space-y-6 pb-12">
    {{-- En-tête --}}
    <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ __('super_admin.dashboard.title') }}</h1>
            <p class="mt-1 text-sm text-slate-500">
                {{ __('super_admin.dashboard.welcome_back', ['name' => auth()->user()?->name ?? 'Super Admin']) }}
            </p>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-2 rounded-lg bg-white px-3 py-2 text-xs font-medium text-slate-600 shadow-sm border border-slate-200">
                <iconify-icon icon="solar:calendar-linear" class="text-slate-400"></iconify-icon>
                {{ now()->translatedFormat('d F Y') }}
            </span>
        </div>
    </header>

    {{-- KPI Cards --}}
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        {{-- Organisations --}}
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">{{ __('super_admin.dashboard.total_orgs') }}</p>
                    <h3 class="mt-2 text-3xl font-bold text-slate-900">{{ $this->stats['total_orgs'] }}</h3>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                    <iconify-icon icon="solar:buildings-bold-duotone" width="24"></iconify-icon>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-2 text-xs">
                <span class="inline-flex items-center gap-1 font-medium text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">
                    <iconify-icon icon="solar:check-circle-bold" width="12"></iconify-icon>
                    {{ $this->stats['active_orgs'] }} {{ __('Actives') }}
                </span>
                @if($this->stats['suspended_orgs'] > 0)
                    <span class="inline-flex items-center gap-1 font-medium text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">
                        {{ $this->stats['suspended_orgs'] }} {{ __('Suspendues') }}
                    </span>
                @endif
            </div>
        </div>

        {{-- Utilisateurs --}}
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">{{ __('super_admin.dashboard.total_users') }}</p>
                    <h3 class="mt-2 text-3xl font-bold text-slate-900">{{ $this->stats['total_users'] }}</h3>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                    <iconify-icon icon="solar:users-group-rounded-bold-duotone" width="24"></iconify-icon>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-2 text-xs text-slate-400">
                <iconify-icon icon="solar:info-circle-linear" width="14"></iconify-icon>
                <span>{{ __('Utilisateurs enregistrés') }}</span>
            </div>
        </div>

        {{-- Tickets --}}
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">{{ __('super_admin.dashboard.total_tickets') }}</p>
                    <h3 class="mt-2 text-3xl font-bold text-slate-900">{{ $this->stats['total_tickets'] }}</h3>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-50 text-violet-600">
                    <iconify-icon icon="solar:ticket-bold-duotone" width="24"></iconify-icon>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-2 text-xs">
                <span class="inline-flex items-center gap-1 font-medium text-violet-600 bg-violet-50 px-2 py-0.5 rounded-full">
                    +{{ $this->stats['tickets_7d'] }}
                </span>
                <span class="text-slate-400">{{ __('cette semaine') }}</span>
            </div>
        </div>

        {{-- Santé Système --}}
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">{{ __('Jobs en échec') }}</p>
                    <h3 class="mt-2 text-3xl font-bold {{ $this->stats['failed_jobs'] > 0 ? 'text-red-600' : 'text-slate-900' }}">{{ $this->stats['failed_jobs'] }}</h3>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl {{ $this->stats['failed_jobs'] > 0 ? 'bg-red-50 text-red-600' : 'bg-slate-50 text-slate-600' }}">
                    <iconify-icon icon="solar:server-square-bold-duotone" width="24"></iconify-icon>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-2 text-xs">
                @if($this->stats['failed_jobs'] > 0)
                    <span class="inline-flex items-center gap-1 font-medium text-red-600 bg-red-50 px-2 py-0.5 rounded-full">
                        <iconify-icon icon="solar:danger-circle-bold" width="12"></iconify-icon>
                        {{ __('Attention requise') }}
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 font-medium text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">
                        <iconify-icon icon="solar:check-circle-bold" width="12"></iconify-icon>
                        {{ __('Système sain') }}
                    </span>
                @endif
            </div>
        </div>
    </section>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Graphique d'activité --}}
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">{{ __('super_admin.dashboard.activity_chart') }}</h3>
                    <p class="text-sm text-slate-500">{{ __('Aperçu des tickets sur 7 jours') }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2 text-xs font-medium">
                        <span class="h-2.5 w-2.5 rounded-full bg-[#005F02]"></span>
                        <span class="text-slate-600">{{ __('Créés') }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs font-medium">
                        <span class="h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
                        <span class="text-slate-600">{{ __('Fermés') }}</span>
                    </div>
                </div>
            </div>

            @php
                $chart = $this->activityChart;
                $labels = $chart['labels'] ?? [];
                $created = $chart['created'] ?? [];
                $closed = $chart['closed'] ?? [];
                $maxVal = max(1, max(array_merge($created ?: [0], $closed ?: [0])));
            @endphp

            <div class="mt-4 flex h-64 items-end justify-between gap-2 sm:gap-4">
                @foreach ($labels as $i => $label)
                    @php
                        $c = (int) ($created[$i] ?? 0);
                        $cl = (int) ($closed[$i] ?? 0);
                        $pctCreated = $maxVal > 0 ? round($c / $maxVal * 100) : 0;
                        $pctClosed = $maxVal > 0 ? round($cl / $maxVal * 100) : 0;
                    @endphp
                    <div class="group relative flex flex-1 flex-col items-center justify-end gap-2 h-full">
                        <!-- Tooltip -->
                        <div class="absolute bottom-full mb-2 hidden flex-col items-center rounded-lg bg-slate-900 px-2 py-1 text-xs text-white shadow-lg group-hover:flex z-10 whitespace-nowrap">
                            <span>{{ $label }}</span>
                            <span class="font-bold text-emerald-400">{{ $c }} créés</span>
                            <span class="font-bold text-emerald-200">{{ $cl }} fermés</span>
                        </div>
                        
                        <div class="flex w-full max-w-[40px] items-end justify-center gap-1 h-full">
                            <div class="w-full rounded-t-md bg-[#005F02] transition-all duration-500 hover:opacity-90" style="height: {{ max(4, $pctCreated) }}%"></div>
                            <div class="w-full rounded-t-md bg-emerald-400 transition-all duration-500 hover:opacity-90" style="height: {{ max(4, $pctClosed) }}%"></div>
                        </div>
                        <span class="text-xs font-medium text-slate-400">{{ $label }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Résumé du jour --}}
        <section class="flex flex-col gap-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm flex-1">
                <h3 class="text-lg font-bold text-slate-900 mb-4">{{ __('Aujourd\'hui') }}</h3>
                
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-600">
                                <iconify-icon icon="solar:ticket-bold" width="20"></iconify-icon>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-slate-900">{{ __('Tickets créés') }}</p>
                                <p class="text-xs text-slate-500">{{ __('Depuis minuit') }}</p>
                            </div>
                        </div>
                        <span class="text-xl font-bold text-slate-900">{{ $this->todayHighlights['tickets_created_today'] }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">
                                <iconify-icon icon="solar:check-circle-bold" width="20"></iconify-icon>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-slate-900">{{ __('Tickets fermés') }}</p>
                                <p class="text-xs text-slate-500">{{ __('Résolus aujourd\'hui') }}</p>
                            </div>
                        </div>
                        <span class="text-xl font-bold text-emerald-600">{{ $this->todayHighlights['tickets_closed_today'] }}</span>
                    </div>

                    <div class="border-t border-slate-100 pt-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-50 text-blue-600">
                                    <iconify-icon icon="solar:clock-circle-bold" width="20"></iconify-icon>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-900">{{ __('Jobs en attente') }}</p>
                                    <p class="text-xs text-slate-500">{{ __('File d\'attente') }}</p>
                                </div>
                            </div>
                            <span class="text-xl font-bold text-blue-600">{{ $this->stats['pending_jobs'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    {{-- Actions récentes --}}
    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
            <h3 class="text-lg font-bold text-slate-900">{{ __('super_admin.dashboard.recent_actions') }}</h3>
            <a href="{{ route('platform-admin.audit-log') }}" class="text-sm font-medium text-[#005F02] hover:text-[#004d02] transition-colors">
                {{ __('super_admin.dashboard.view_all') }}
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                    <tr>
                        <th class="px-6 py-3 font-semibold">{{ __('Action') }}</th>
                        <th class="px-6 py-3 font-semibold">{{ __('Utilisateur') }}</th>
                        <th class="px-6 py-3 font-semibold text-right">{{ __('Date') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($this->recentActions as $action)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-500">
                                        <iconify-icon icon="solar:shield-star-bold" width="14"></iconify-icon>
                                    </div>
                                    @php
                                        $actionKey = 'super_admin.audit.action_' . str_replace('.', '_', $action->action);
                                        $actionLabel = __($actionKey) !== $actionKey ? __($actionKey) : $action->action;
                                    @endphp
                                    <span class="font-medium text-slate-900">{{ $actionLabel }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                {{ $action->user_name ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-right text-slate-500 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($action->created_at)->diffForHumans() }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center">
                                    <iconify-icon icon="solar:clipboard-list-linear" width="32" class="mb-2 opacity-50"></iconify-icon>
                                    <p>{{ __('super_admin.dashboard.no_recent_actions') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>