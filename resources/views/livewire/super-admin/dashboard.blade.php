<div class="space-y-8 pb-12">
    {{-- En-tête --}}
    <header>
        <h1 class="text-2xl font-bold text-slate-900">{{ __('super_admin.dashboard.title') }}</h1>
        <p class="mt-1 text-sm text-slate-500">{{ __('super_admin.dashboard.subtitle') }}</p>
        <p class="mt-1 text-xs text-slate-400">{{ __('super_admin.dashboard.welcome_back', ['name' => auth()->user()?->name ?? 'Super Admin']) }} · {{ now()->translatedFormat('l d F Y') }}</p>
    </header>

    {{-- Chiffres clés (une ligne) --}}
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <a href="{{ route('platform-admin.organizations') }}" class="flex items-center gap-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm hover:border-indigo-200 hover:shadow transition">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-indigo-100">
                <iconify-icon icon="solar:buildings-bold-duotone" width="24" class="text-indigo-600"></iconify-icon>
            </div>
            <div>
                <p class="text-2xl font-bold tabular-nums text-slate-900">{{ $this->stats['total_orgs'] }}</p>
                <p class="text-xs font-medium text-slate-500">{{ __('super_admin.dashboard.total_orgs') }}</p>
            </div>
        </a>
        <div class="flex items-center gap-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-sky-100">
                <iconify-icon icon="solar:users-group-rounded-bold-duotone" width="24" class="text-sky-600"></iconify-icon>
            </div>
            <div>
                <p class="text-2xl font-bold tabular-nums text-slate-900">{{ $this->stats['total_users'] }}</p>
                <p class="text-xs font-medium text-slate-500">{{ __('super_admin.dashboard.total_users') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-violet-100">
                <iconify-icon icon="solar:ticket-bold-duotone" width="24" class="text-violet-600"></iconify-icon>
            </div>
            <div>
                <p class="text-2xl font-bold tabular-nums text-slate-900">{{ $this->stats['total_tickets'] }}</p>
                <p class="text-xs font-medium text-slate-500">{{ __('super_admin.dashboard.total_tickets') }}</p>
            </div>
        </div>
    </section>

    {{-- Organisations --}}
    <section>
        <h2 class="mb-3 text-sm font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.dashboard.organizations_section') }}</h2>
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
            <a href="{{ route('platform-admin.organizations') }}" class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm hover:border-indigo-200 hover:shadow transition">
                <p class="text-xs font-medium text-slate-500">{{ __('super_admin.dashboard.total_orgs') }}</p>
                <p class="mt-1 text-2xl font-bold tabular-nums text-slate-900">{{ $this->stats['total_orgs'] }}</p>
            </a>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium text-slate-500">{{ __('super_admin.dashboard.active_orgs') }}</p>
                <p class="mt-1 text-2xl font-bold tabular-nums text-emerald-600">{{ $this->stats['active_orgs'] }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium text-slate-500">{{ __('super_admin.dashboard.suspended_orgs') }}</p>
                <p class="mt-1 text-2xl font-bold tabular-nums text-amber-600">{{ $this->stats['suspended_orgs'] }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium text-slate-500">{{ __('super_admin.dashboard.disabled_orgs') }}</p>
                <p class="mt-1 text-2xl font-bold tabular-nums text-red-600">{{ $this->stats['disabled_orgs'] }}</p>
            </div>
        </div>
    </section>

    {{-- Utilisateurs & tickets --}}
    <section>
        <h2 class="mb-3 text-sm font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.dashboard.users_tickets_section') }}</h2>
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium text-slate-500">{{ __('super_admin.dashboard.total_users') }}</p>
                <p class="mt-1 text-2xl font-bold tabular-nums text-slate-900">{{ $this->stats['total_users'] }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium text-slate-500">{{ __('super_admin.dashboard.total_tickets') }}</p>
                <p class="mt-1 text-2xl font-bold tabular-nums text-slate-900">{{ $this->stats['total_tickets'] }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium text-slate-500">{{ __('super_admin.dashboard.tickets_7d') }}</p>
                <p class="mt-1 text-2xl font-bold tabular-nums text-cyan-600">{{ $this->stats['tickets_7d'] }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium text-slate-500">{{ __('super_admin.dashboard.tickets_closed_7d') }}</p>
                <p class="mt-1 text-2xl font-bold tabular-nums text-teal-600">{{ $this->stats['tickets_closed_7d'] }}</p>
            </div>
        </div>
    </section>

    {{-- Aujourd'hui + Système (une ligne) --}}
    <section class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.dashboard.today_highlights') }}</h3>
            <div class="mt-3 flex gap-6">
                <div>
                    <p class="text-xl font-bold tabular-nums text-slate-900">{{ $this->todayHighlights['tickets_created_today'] }}</p>
                    <p class="text-xs text-slate-500">{{ __('super_admin.dashboard.tickets_created_today') }}</p>
                </div>
                <div>
                    <p class="text-xl font-bold tabular-nums text-emerald-600">{{ $this->todayHighlights['tickets_closed_today'] }}</p>
                    <p class="text-xs text-slate-500">{{ __('super_admin.dashboard.tickets_closed_today') }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm {{ $this->stats['failed_jobs'] > 0 ? 'border-red-200 bg-red-50/50' : '' }}">
            <p class="text-xs font-medium text-slate-500">{{ __('super_admin.dashboard.failed_jobs') }}</p>
            <p class="mt-1 text-2xl font-bold tabular-nums {{ $this->stats['failed_jobs'] > 0 ? 'text-red-600' : 'text-emerald-600' }}">{{ $this->stats['failed_jobs'] }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-medium text-slate-500">{{ __('super_admin.dashboard.pending_jobs') }}</p>
            <p class="mt-1 text-2xl font-bold tabular-nums text-slate-900">{{ $this->stats['pending_jobs'] }}</p>
        </div>
    </section>

    {{-- Graphique d'activité (7 jours) --}}
    @php
        $chart = $this->activityChart;
        $labels = $chart['labels'] ?? [];
        $created = $chart['created'] ?? [];
        $closed = $chart['closed'] ?? [];
        $maxVal = max(1, max(array_merge($created ?: [0], $closed ?: [0])));
    @endphp
    <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-800">{{ __('super_admin.dashboard.activity_chart') }}</h3>
        <div class="mt-2 flex items-center gap-4 text-xs text-slate-500">
            <span class="inline-flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-indigo-500"></span> {{ __('super_admin.dashboard.chart_created') }}</span>
            <span class="inline-flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-emerald-500"></span> {{ __('super_admin.dashboard.chart_closed') }}</span>
        </div>
        <div class="mt-4 flex items-end justify-between gap-2 border-b border-slate-100 pb-2" style="min-height: 120px;">
            @foreach ($labels as $i => $label)
                @php
                    $c = (int) ($created[$i] ?? 0);
                    $cl = (int) ($closed[$i] ?? 0);
                    $pctCreated = $maxVal > 0 ? round($c / $maxVal * 100) : 0;
                    $pctClosed = $maxVal > 0 ? round($cl / $maxVal * 100) : 0;
                @endphp
                <div class="flex flex-1 flex-col items-center gap-1 min-w-0">
                    <div class="flex w-full items-end justify-center gap-0.5" style="height: 100px;">
                        <div class="w-3 rounded-t bg-indigo-400" style="height: {{ max(6, $pctCreated) }}%; min-height: 4px;"></div>
                        <div class="w-3 rounded-t bg-emerald-400" style="height: {{ max(6, $pctClosed) }}%; min-height: 4px;"></div>
                    </div>
                    <span class="text-[10px] font-medium text-slate-400">{{ $label }}</span>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Actions récentes --}}
    <section class="rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <h3 class="text-sm font-semibold text-slate-800">{{ __('super_admin.dashboard.recent_actions') }}</h3>
            <a href="{{ route('platform-admin.audit-log') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">{{ __('super_admin.dashboard.view_all') }}</a>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse ($this->recentActions as $action)
                <div class="flex items-center justify-between gap-4 px-5 py-3 hover:bg-slate-50/50">
                    <div class="flex min-w-0 items-center gap-3">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-indigo-100">
                            <iconify-icon icon="solar:shield-star-bold-duotone" width="16" class="text-indigo-600"></iconify-icon>
                        </div>
                        <div>
                            @php
                                $actionKey = 'super_admin.audit.action_' . str_replace('.', '_', $action->action);
                                $actionLabel = __($actionKey) !== $actionKey ? __($actionKey) : $action->action;
                            @endphp
                            <p class="text-sm font-medium text-slate-800">{{ $actionLabel }}</p>
                            <p class="text-xs text-slate-500">{{ $action->user_name ?? '—' }}</p>
                        </div>
                    </div>
                    <span class="shrink-0 text-xs text-slate-400">{{ \Carbon\Carbon::parse($action->created_at)->diffForHumans() }}</span>
                </div>
            @empty
                <div class="px-5 py-10 text-center text-sm text-slate-400">{{ __('super_admin.dashboard.no_recent_actions') }}</div>
            @endforelse
        </div>
    </section>
</div>
