<div class="w-full max-w-full min-w-0 mx-auto">
    {{-- Header --}}
    <div class="page-header">
        <div class="min-w-0">
            <h1 class="page-title">{{ __('pages.groups.title') }}</h1>
            <p class="page-subtitle">{{ __('pages.groups.subtitle') }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('tickets.index') }}" wire:navigate
               class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2.5 sm:px-4 text-sm font-semibold text-slate-600 shadow-sm hover:bg-slate-50 hover:text-slate-900 transition-all">
                <iconify-icon icon="solar:ticket-bold-duotone" width="18"></iconify-icon>
                <span class="hidden sm:inline">{{ __('pages.groups.all_tickets') }}</span>
            </a>
        </div>
    </div>

    @if($groups->isEmpty() && !$ungroupedStats)
        {{-- Empty state --}}
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
        <div class="flex flex-col lg:flex-row gap-5 lg:gap-6">
            {{-- ═══ MAIN COLUMN — Groups with top tickets ═══ --}}
            <div class="flex-1 min-w-0 space-y-4">

                @foreach($groups as $grp)
                    @php
                        $gid = (int) $grp->id;
                        $totalActive = (int) $grp->open_count + (int) $grp->in_progress_count + (int) $grp->pending_count;
                        $color = $grp->color ?? 'var(--accent)';
                        $unassigned = (int) ($grp->unassigned_count ?? 0);
                        $slaBreach = (int) ($grp->sla_breached_count ?? 0);
                        $tickets = $topTicketsByGroup[$gid] ?? [];
                        $agents = $agentsByGroup[$gid] ?? [];
                        $hasAlerts = $unassigned > 0 || $slaBreach > 0;
                    @endphp

                    @if($totalActive > 0)
                        {{-- Active group: expanded with tickets --}}
                        <div class="rounded-xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                            {{-- Group header --}}
                            <div class="px-5 py-4 flex items-center gap-3">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-white text-xs font-bold" style="background-color: {{ $color }};">
                                    @if($grp->icon)
                                        <iconify-icon icon="{{ $grp->icon }}" width="16"></iconify-icon>
                                    @else
                                        {{ strtoupper(mb_substr($grp->name, 0, 1)) }}
                                    @endif
                                </span>
                                <div class="min-w-0 flex-1">
                                    <h3 class="text-sm font-bold text-slate-900">{{ $grp->name }}</h3>
                                    @if($hasAlerts)
                                        <div class="flex items-center gap-2 mt-0.5 text-[11px]">
                                            @if($slaBreach > 0)
                                                <span class="text-red-600 font-medium">{{ $slaBreach }} {{ __('en retard SLA') }}</span>
                                            @endif
                                            @if($unassigned > 0)
                                                <span class="text-amber-600 font-medium">{{ $unassigned }} {{ __('non assigné(s)') }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                <span class="text-xs text-slate-400 font-medium shrink-0">{{ $totalActive }} {{ __('actif(s)') }}</span>
                            </div>

                            {{-- Top tickets --}}
                            @if(count($tickets) > 0)
                                <div class="border-t border-slate-100">
                                    @foreach($tickets as $t)
                                        @php
                                            $pLevel = (int) ($t->priority_level ?? 2);
                                            $age = \Carbon\Carbon::parse($t->created_at)->diffForHumans(short: true);
                                            $isHighPriority = $pLevel >= 3;
                                        @endphp
                                        <a href="{{ route('tickets.discussion', ['ticket' => $t->public_id]) }}" wire:navigate
                                           class="flex items-center gap-3 px-5 py-2.5 hover:bg-slate-50 transition-colors border-b border-slate-50 last:border-b-0">
                                            <span class="font-mono text-[11px] text-slate-400 shrink-0 w-16">{{ \Illuminate\Support\Str::limit($t->public_id, 10) }}</span>
                                            <span class="text-sm text-slate-900 font-medium truncate flex-1 min-w-0">{{ $t->subject }}</span>
                                            @if($isHighPriority)
                                                <span class="shrink-0 inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-medium {{ $pLevel >= 4 ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                                    {{ $t->priority_name ?? '' }}
                                                </span>
                                            @endif
                                            @if($t->sla_resolution_breached)
                                                <span class="shrink-0 text-[10px] font-medium text-red-600">SLA</span>
                                            @endif
                                            @if($t->assignee_name)
                                                <span class="shrink-0 text-xs text-slate-500 max-w-[6rem] truncate hidden sm:inline">{{ $t->assignee_name }}</span>
                                            @else
                                                <span class="shrink-0 text-xs text-red-500 font-medium hidden sm:inline">{{ __('Non assigné') }}</span>
                                            @endif
                                            <span class="shrink-0 text-[10px] text-slate-400 w-8 text-right">{{ $age }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Footer link --}}
                            <div class="px-5 py-2.5 border-t border-slate-100 bg-slate-50/50">
                                <a href="{{ route('tickets.index', ['group' => $grp->id]) }}" wire:navigate class="text-xs font-medium text-slate-500 hover:text-slate-900 transition-colors">
                                    {{ __('Voir tous les tickets') }} &rarr;
                                </a>
                            </div>
                        </div>
                    @else
                        {{-- Inactive group: compact single line --}}
                        <a href="{{ route('tickets.index', ['group' => $grp->id]) }}" wire:navigate
                           class="flex items-center gap-3 rounded-xl bg-white border border-slate-200 px-5 py-3 hover:bg-slate-50 transition-colors shadow-sm">
                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg text-white text-xs font-bold" style="background-color: {{ $color }}; opacity: 0.6;">
                                @if($grp->icon)
                                    <iconify-icon icon="{{ $grp->icon }}" width="14"></iconify-icon>
                                @else
                                    {{ strtoupper(mb_substr($grp->name, 0, 1)) }}
                                @endif
                            </span>
                            <span class="text-sm font-medium text-slate-600">{{ $grp->name }}</span>
                            <span class="ml-auto text-xs text-slate-400">0 {{ __('actif') }}</span>
                        </a>
                    @endif
                @endforeach

                {{-- Ungrouped --}}
                @if($ungroupedStats)
                    @php
                        $ungroupedTotal = (int) $ungroupedStats->open_count + (int) $ungroupedStats->in_progress_count + (int) $ungroupedStats->pending_count;
                    @endphp
                    @if($ungroupedTotal > 0)
                        <div class="rounded-xl bg-white border border-dashed border-slate-300 shadow-sm overflow-hidden">
                            <div class="px-5 py-4 flex items-center gap-3">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-400 text-xs font-bold">
                                    <iconify-icon icon="solar:minus-circle-linear" width="16"></iconify-icon>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <h3 class="text-sm font-bold text-slate-700">{{ __('pages.groups.ungrouped') }}</h3>
                                </div>
                                <span class="text-xs text-slate-400 font-medium shrink-0">{{ $ungroupedTotal }} {{ __('actif(s)') }}</span>
                            </div>
                            <div class="px-5 py-2.5 border-t border-slate-100 bg-slate-50/50">
                                <a href="{{ route('tickets.index', ['group' => 'none']) }}" wire:navigate class="text-xs font-medium text-slate-500 hover:text-slate-900 transition-colors">
                                    {{ __('Voir tous les tickets') }} &rarr;
                                </a>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('tickets.index', ['group' => 'none']) }}" wire:navigate
                           class="flex items-center gap-3 rounded-xl bg-white border border-dashed border-slate-300 px-5 py-3 hover:bg-slate-50 transition-colors shadow-sm">
                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-400 text-xs font-bold">
                                <iconify-icon icon="solar:minus-circle-linear" width="14"></iconify-icon>
                            </span>
                            <span class="text-sm font-medium text-slate-500">{{ __('pages.groups.ungrouped') }}</span>
                            <span class="ml-auto text-xs text-slate-400">0 {{ __('actif') }}</span>
                        </a>
                    @endif
                @endif
            </div>

            {{-- ═══ SIDEBAR — Alerts & Team ═══ --}}
            <div class="lg:w-72 xl:w-80 shrink-0 space-y-4">

                {{-- Alerts --}}
                @if(count($alerts) > 0)
                    <div class="rounded-xl bg-white border border-slate-200 shadow-sm p-4">
                        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">{{ __('Alertes') }}</h4>
                        <div class="space-y-2">
                            @foreach($alerts as $alert)
                                <div class="flex items-center gap-2.5 text-sm">
                                    <iconify-icon icon="{{ $alert['icon'] }}" width="16" class="{{ $alert['type'] === 'danger' ? 'text-red-500' : 'text-amber-500' }}"></iconify-icon>
                                    <span class="{{ $alert['type'] === 'danger' ? 'text-red-700' : 'text-amber-700' }} font-medium">{{ $alert['text'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Team overview across all groups --}}
                @php
                    $allAgents = collect();
                    foreach ($agentsByGroup as $gAgents) {
                        foreach ($gAgents as $a) {
                            $existing = $allAgents->firstWhere('user_id', $a->user_id);
                            if ($existing) {
                                $existing->ticket_count = (int) $existing->ticket_count + (int) $a->ticket_count;
                            } else {
                                $allAgents->push((object) ['user_id' => $a->user_id, 'user_name' => $a->user_name, 'ticket_count' => (int) $a->ticket_count]);
                            }
                        }
                    }
                    $allAgents = $allAgents->sortByDesc('ticket_count')->values();
                @endphp

                @if($allAgents->isNotEmpty())
                    <div class="rounded-xl bg-white border border-slate-200 shadow-sm p-4">
                        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">{{ __('Charge par agent') }}</h4>
                        <div class="space-y-2">
                            @foreach($allAgents->take(10) as $agent)
                                <div class="flex items-center gap-2.5">
                                    <div class="h-7 w-7 rounded-full flex items-center justify-center text-[10px] font-bold shrink-0" style="background: var(--accent-soft); color: var(--accent);">
                                        {{ strtoupper(mb_substr($agent->user_name, 0, 2)) }}
                                    </div>
                                    <span class="text-sm text-slate-700 font-medium truncate flex-1 min-w-0">{{ $agent->user_name }}</span>
                                    <span class="text-xs text-slate-500 font-medium shrink-0 tabular-nums">{{ $agent->ticket_count }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Quick stats --}}
                @php
                    $totalOpen = $groups->sum(fn ($g) => (int) $g->open_count) + (int) ($ungroupedStats->open_count ?? 0);
                    $totalInProgress = $groups->sum(fn ($g) => (int) $g->in_progress_count) + (int) ($ungroupedStats->in_progress_count ?? 0);
                    $totalPending = $groups->sum(fn ($g) => (int) $g->pending_count) + (int) ($ungroupedStats->pending_count ?? 0);
                @endphp
                <div class="rounded-xl bg-white border border-slate-200 shadow-sm p-4">
                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">{{ __('Vue d\'ensemble') }}</h4>
                    <div class="space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-600">{{ __('Ouverts') }}</span>
                            <span class="text-sm font-bold text-slate-900 tabular-nums">{{ $totalOpen }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-600">{{ __('En cours') }}</span>
                            <span class="text-sm font-bold text-slate-900 tabular-nums">{{ $totalInProgress }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-600">{{ __('En attente') }}</span>
                            <span class="text-sm font-bold text-slate-900 tabular-nums">{{ $totalPending }}</span>
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                            <span class="text-sm text-slate-700 font-medium">{{ __('Total actifs') }}</span>
                            <span class="text-sm font-bold text-slate-900 tabular-nums">{{ $totalOpen + $totalInProgress + $totalPending }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
