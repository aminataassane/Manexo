{{-- Dashboard dynamique (Livewire) --}}
@php
    $user = Auth::user();
    $org = $this->organization;
    $orgName = $org?->name ?? '—';
    $role = $this->role;
    $kpis = $this->kpis;
    $chartData = $this->chartData;
    $priorityTickets = $this->priorityTickets;
    $recentDiscussions = $this->recentDiscussions;
    $discussionsUnreadCount = $this->discussionsUnreadCount;
    $recentActivity = $this->recentActivity;
    $pendingFormAssignments = $this->pendingFormAssignments;
    $pendingFormsCount = $this->pendingFormsCount;
@endphp

<div class="space-y-4 sm:space-y-6">
<!-- WELCOME SECTION -->
<div class="relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 shadow-sm border border-slate-100 sm:p-6 lg:p-8 xl:p-10 min-[1920px]:p-12">
    <div class="relative z-10 flex flex-col items-start justify-between gap-4 sm:gap-6 sm:flex-row sm:items-center">
        <div class="min-w-0 w-full sm:w-auto">
            <h1 class="text-xl font-bold text-slate-900 sm:text-2xl lg:text-3xl min-[1920px]:text-4xl tracking-tight break-words">
                {{ __('pages.dashboard.hello') }}, <span class="text-transparent bg-clip-text bg-gradient-to-r from-[var(--accent)] to-[var(--accent-dark)]">{{ $user->name }}</span> 👋
            </h1>
            <p class="mt-1 sm:mt-2 text-sm sm:text-base text-slate-500 max-w-2xl">
                {{ __('pages.dashboard.intro', ['org' => $orgName]) }}
            </p>
        </div>
        <div class="flex flex-col gap-2 w-full sm:w-auto sm:flex-row sm:flex-shrink-0">
            <a href="{{ route('tickets.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-4 py-3 sm:py-2.5 text-sm font-semibold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-200 hover:bg-slate-50 transition-all touch-target sm:min-h-0 sm:min-w-0">
                <iconify-icon icon="solar:list-bold" width="18"></iconify-icon>
                {{ __('pages.dashboard.view_tickets') }}
            </a>
            <a href="{{ route('tickets.create') }}" class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-3 sm:py-2.5 text-sm font-semibold text-white shadow-lg shadow-[var(--accent-ring)] hover:opacity-90 transition-all transform hover:-translate-y-0.5 touch-target sm:min-h-0 sm:min-w-0" style="background-color: var(--accent);">
                <iconify-icon icon="solar:add-circle-bold" width="18"></iconify-icon>
                {{ __('pages.dashboard.new_ticket') }}
            </a>
        </div>
    </div>
    <div class="absolute top-0 right-0 -mt-20 -mr-20 h-64 w-64 rounded-full bg-[var(--accent-soft)] opacity-20 blur-3xl"></div>
    <div class="absolute bottom-0 left-0 -mb-20 -ml-20 h-64 w-64 rounded-full bg-blue-50 opacity-50 blur-3xl"></div>
</div>

@if ($this->isAdminView)
    <!-- KPI CARDS -->
    <div class="grid grid-cols-2 gap-3 sm:gap-4 sm:grid-cols-2 lg:grid-cols-4 lg:gap-6 min-[1920px]:gap-8">
        <div class="group relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 lg:p-6 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 min-w-0">
            <div class="flex items-center justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-slate-500 truncate">{{ __('pages.dashboard.open_tickets') }}</p>
                    <p class="mt-1 sm:mt-2 text-2xl sm:text-3xl min-[1920px]:text-4xl font-bold text-slate-900">{{ $kpis['open'] }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-50 text-red-600 group-hover:scale-110 transition-transform duration-300">
                    <iconify-icon icon="solar:danger-circle-bold-duotone" width="24"></iconify-icon>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs font-medium text-red-600">
                <span class="flex items-center gap-1 bg-red-50 px-2 py-1 rounded-full">
                    <iconify-icon icon="solar:arrow-right-up-linear" width="12"></iconify-icon>
                    {{ __('pages.dashboard.action_required') }}
                </span>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 lg:p-6 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 min-w-0">
            <div class="flex items-center justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-slate-500 truncate">{{ __('pages.dashboard.in_progress') }}</p>
                    <p class="mt-1 sm:mt-2 text-2xl sm:text-3xl min-[1920px]:text-4xl font-bold text-slate-900">{{ $kpis['in_progress'] }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600 group-hover:scale-110 transition-transform duration-300">
                    <iconify-icon icon="solar:clock-circle-bold-duotone" width="24"></iconify-icon>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs font-medium text-blue-600">
                <span class="flex items-center gap-1 bg-blue-50 px-2 py-1 rounded-full">{{ __('pages.dashboard.active_now') }}</span>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 lg:p-6 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 min-w-0">
            <div class="flex items-center justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-slate-500 truncate">{{ __('pages.dashboard.pending') }}</p>
                    <p class="mt-1 sm:mt-2 text-2xl sm:text-3xl min-[1920px]:text-4xl font-bold text-slate-900">{{ $kpis['pending'] }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-600 group-hover:scale-110 transition-transform duration-300">
                    <iconify-icon icon="solar:pause-circle-bold-duotone" width="24"></iconify-icon>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs font-medium text-amber-600">
                <span class="flex items-center gap-1 bg-amber-50 px-2 py-1 rounded-full">{{ __('pages.dashboard.customer_reply') }}</span>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 lg:p-6 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 min-w-0">
            <div class="flex items-center justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-slate-500 truncate">{{ __('pages.dashboard.resolved_7d') }}</p>
                    <p class="mt-1 sm:mt-2 text-2xl sm:text-3xl min-[1920px]:text-4xl font-bold text-slate-900">{{ $kpis['resolved7d'] }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 group-hover:scale-110 transition-transform duration-300">
                    <iconify-icon icon="solar:check-circle-bold-duotone" width="24"></iconify-icon>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs font-medium text-emerald-600">
                <span class="flex items-center gap-1 bg-emerald-50 px-2 py-1 rounded-full">
                    <iconify-icon icon="solar:graph-up-linear" width="12"></iconify-icon>
                    {{ __('pages.dashboard.productivity') }}
                </span>
            </div>
        </div>
    </div>

    <!-- MAIN GRID -->
    <div class="grid grid-cols-1 gap-4 sm:gap-6 lg:grid-cols-3 min-[1920px]:gap-8">
        <div class="space-y-4 sm:space-y-6 lg:col-span-2 min-w-0">
            <!-- CHART (dynamic 7j / 30j) -->
            <div class="rounded-xl sm:rounded-2xl bg-white p-4 sm:p-6 shadow-sm border border-slate-100 min-w-0 overflow-hidden">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-4 sm:mb-6">
                    <h3 class="text-base sm:text-lg font-bold text-slate-900">{{ __('pages.dashboard.weekly_activity') }}</h3>
                    <div class="flex bg-slate-100 p-1 rounded-lg w-fit">
                        <button type="button" wire:click="setChartDays(7)" class="px-3 py-1.5 sm:py-1 text-xs font-semibold rounded-md transition-colors {{ $chartDays === 7 ? 'bg-white shadow-sm text-slate-800' : 'text-slate-500 hover:text-slate-800' }}">{{ __('pages.dashboard.days_7') }}</button>
                        <button type="button" wire:click="setChartDays(30)" class="px-3 py-1.5 sm:py-1 text-xs font-medium rounded-md transition-colors {{ $chartDays === 30 ? 'bg-white shadow-sm text-slate-800' : 'text-slate-500 hover:text-slate-800' }}">{{ __('pages.dashboard.days_30') }}</button>
                    </div>
                </div>
                <div class="relative h-48 sm:h-56 lg:h-64 min-[1920px]:h-72 w-full flex items-end justify-between gap-0.5 sm:gap-1 px-0 min-w-0 overflow-x-auto">
                    <div class="absolute inset-0 flex flex-col justify-between pointer-events-none">
                        @for ($i = 0; $i < 5; $i++)
                            <div class="border-t border-slate-100 w-full h-0"></div>
                        @endfor
                    </div>
                    @foreach ($chartData as $bar)
                        <div class="relative z-10 flex flex-col items-center gap-2 flex-1 min-w-0 group cursor-default">
                            <div class="relative w-full max-w-[32px] sm:max-w-[40px] h-40 sm:h-44 flex items-end justify-center mx-auto">
                                <div class="w-1.5 sm:w-2.5 rounded-full bg-slate-100 transition-all" style="height: 100%"></div>
                                <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-1.5 sm:w-2.5 rounded-full transition-all shadow-lg shadow-[var(--accent-ring)]" style="background-color: var(--accent); height: {{ max(4, $bar['pct']) }}%"></div>
                            </div>
                            <span class="text-[10px] sm:text-xs font-medium text-slate-400 truncate w-full text-center" title="{{ $bar['count'] }}">{{ $bar['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- PRIORITY TICKETS -->
            <div class="rounded-xl sm:rounded-2xl bg-white shadow-sm border border-slate-100 overflow-hidden min-w-0">
                <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-slate-100 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <h3 class="text-base sm:text-lg font-bold text-slate-900">{{ __('pages.dashboard.priority_tickets') }}</h3>
                    <a href="{{ route('tickets.index') }}" class="text-sm font-semibold hover:underline w-fit" style="color: var(--accent);">{{ __('pages.dashboard.see_all') }}</a>
                </div>
                <div class="responsive-table-wrap">
                    <table class="w-full text-left min-w-[600px] sm:min-w-0">
                        <thead class="bg-slate-50/50 text-xs uppercase text-slate-500 font-semibold">
                            <tr>
                                <th class="px-4 sm:px-6 py-3">{{ __('pages.dashboard.subject') }}</th>
                                <th class="px-4 sm:px-6 py-3">{{ __('pages.dashboard.status') }}</th>
                                <th class="px-4 sm:px-6 py-3">{{ __('pages.dashboard.priority') }}</th>
                                <th class="px-4 sm:px-6 py-3 text-right">{{ __('pages.dashboard.last_activity') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($priorityTickets as $t)
                                @php
                                    $statusValue = is_object($t->status) ? $t->status->value : (string) $t->status;
                                    $pill = $statusPill($statusValue);
                                @endphp
                                <tr class="group hover:bg-slate-50/80 transition-colors cursor-pointer" onclick="window.location='{{ route('tickets.discussion', $t) }}'">
                                    <td class="px-4 sm:px-6 py-3 sm:py-4">
                                        <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                                            <div class="h-8 w-8 shrink-0 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 font-mono text-xs font-bold">{{ $t->shortReference() }}</div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-semibold text-slate-900 group-hover:text-[var(--accent)] transition-colors truncate">{{ $t->subject }}</p>
                                                <p class="text-xs text-slate-500 truncate max-w-[180px] sm:max-w-[200px]">{{ $t->description }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border {{ $pill['bg'] }} {{ $pill['text'] }} {{ $pill['border'] }}">
                                            {{ $statusLabel($statusValue) }}
                                        </span>
                                    </td>
                                    <td class="px-4 sm:px-6 py-3 sm:py-4">
                                        <div class="flex items-center gap-1.5">
                                            <div class="h-2 w-2 shrink-0 rounded-full" style="background-color: {{ $t->priority_color ?? '#cbd5e1' }}"></div>
                                            <span class="text-sm text-slate-700">{{ $t->priority_name ?? __('pages.dashboard.normal_priority') }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 sm:px-6 py-3 sm:py-4 text-right text-sm text-slate-500 whitespace-nowrap">{{ $t->updated_at?->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 sm:px-6 py-8 text-center text-slate-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <iconify-icon icon="solar:ticket-linear" width="32" class="mb-2 opacity-50"></iconify-icon>
                                            <p>{{ __('pages.dashboard.no_priority_tickets') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: Discussions + Activity -->
        <div class="space-y-4 sm:space-y-6 min-w-0">
            <!-- DISCUSSIONS (dynamic) -->
            <div class="rounded-xl sm:rounded-2xl bg-white p-4 sm:p-6 shadow-sm border border-slate-100">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-slate-900">{{ __('pages.dashboard.discussions') }}</h3>
                    @if ($discussionsUnreadCount > 0)
                        <span class="flex h-6 min-w-[24px] px-1.5 items-center justify-center rounded-full bg-red-100 text-xs font-bold text-red-600">{{ $discussionsUnreadCount }}</span>
                    @endif
                </div>
                <div class="space-y-4">
                    @forelse ($recentDiscussions as $d)
                        <a href="{{ $d->url }}" class="flex gap-4 p-3 rounded-xl hover:bg-slate-50 transition-colors cursor-pointer group block">
                            <div class="relative shrink-0">
                                <div class="h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center text-sm font-semibold text-slate-600 ring-2 ring-white" style="background: var(--accent-soft); color: var(--accent);">{{ strtoupper(mb_substr($d->name ?: 'D', 0, 1)) }}</div>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between mb-1">
                                    <p class="text-sm font-bold text-slate-900 truncate">{{ $d->name ?: __('pages.dashboard.discussions') }}</p>
                                    <span class="text-xs text-slate-400 shrink-0">{{ $d->last_at?->diffForHumans() ?: '—' }}</span>
                                </div>
                                <p class="text-xs text-slate-500 line-clamp-2 group-hover:text-slate-700">{{ $d->last_body ?: __('pages.dashboard.no_message') }}</p>
                            </div>
                        </a>
                    @empty
                        <p class="text-sm text-slate-500 py-4 text-center">{{ __('pages.dashboard.no_recent_discussion') }}</p>
                    @endforelse
                </div>
                <a href="{{ route('discussions.index') }}" class="mt-6 w-full flex rounded-xl border border-slate-200 bg-white py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors justify-center">
                    {{ __('pages.dashboard.see_all_discussions') }}
                </a>
            </div>

            <!-- PENDING FORMS -->
            @if($pendingFormsCount > 0)
                <div class="rounded-xl sm:rounded-2xl bg-white p-4 sm:p-6 shadow-sm border border-slate-100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base sm:text-lg font-bold text-slate-900">{{ __('pages.dashboard.forms_to_fill') }}</h3>
                        <a href="{{ route('forms.index') }}" class="text-sm font-semibold hover:underline w-fit" style="color: var(--accent);">{{ __('pages.dashboard.see_all_forms') }}</a>
                    </div>
                    <ul class="space-y-3">
                        @foreach($pendingFormAssignments as $fa)
                            @php
                                $faStatus = $fa->status instanceof \App\Enums\FormAssignmentStatus ? $fa->status->value : (string) $fa->status;
                                $isOverdue = $faStatus === 'overdue';
                            @endphp
                            <li>
                                <a href="{{ route('forms.fill', $fa) }}" class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 bg-slate-50/30 hover:bg-slate-50/60 transition-colors shadow-sm group block">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $isOverdue ? 'bg-red-50 text-red-600' : 'bg-[var(--accent-soft)] text-[var(--accent)]' }}">
                                        <iconify-icon icon="{{ $isOverdue ? 'solar:alarm-bold-duotone' : 'solar:clipboard-text-bold-duotone' }}" width="20"></iconify-icon>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-semibold text-slate-900 truncate group-hover:text-[var(--accent)] transition-colors">{{ $fa->form?->name ?? '—' }}</p>
                                        <div class="flex flex-wrap items-center gap-x-3 gap-y-0.5 mt-0.5 text-xs text-slate-500">
                                            @if($isOverdue)
                                                <span class="font-semibold text-red-600">{{ __('pages.dashboard.past_due') }}</span>
                                            @endif
                                            @if($fa->due_date)
                                                <span class="flex items-center gap-1">
                                                    <iconify-icon icon="solar:calendar-linear" width="12"></iconify-icon>
                                                    {{ __('pages.dashboard.due_date') }}: {{ $fa->due_date->format('d/m/Y') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium border shrink-0 {{ $isOverdue ? 'bg-red-50 text-red-700 border-red-100' : 'bg-[var(--accent-soft)] text-[var(--accent)] border-[var(--accent)]/20' }}">
                                        {{ $isOverdue ? __('pages.dashboard.past_due') : __('pages.forms.status_pending') }}
                                    </span>
                                    <iconify-icon icon="solar:arrow-right-linear" width="18" class="text-slate-400 group-hover:text-[var(--accent)] transition-colors shrink-0"></iconify-icon>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- RECENT ACTIVITY (dynamic) -->
            <div class="rounded-xl sm:rounded-2xl bg-white p-4 sm:p-6 shadow-sm border border-slate-100">
                <h3 class="text-base sm:text-lg font-bold text-slate-900 mb-4 sm:mb-6">{{ __('pages.dashboard.recent_activity') }}</h3>
                <div class="relative pl-4 space-y-6 before:absolute before:left-[19px] before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-100">
                    @forelse ($recentActivity as $a)
                        <a href="{{ $a->url }}" class="relative pl-6 block group">
                            <div class="absolute left-0 top-1.5 h-2.5 w-2.5 rounded-full border-2 border-white shadow-sm group-hover:scale-110 transition-transform" style="background-color: var(--accent);"></div>
                            <p class="text-sm font-medium text-slate-900 group-hover:text-[var(--accent)] truncate" title="{{ $a->subject }}">{{ $a->subject }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $a->created_at->diffForHumans() }} {{ __('pages.dashboard.by') }} <span class="font-medium text-slate-700">{{ $a->user_name }}</span></p>
                        </a>
                    @empty
                        <p class="text-sm text-slate-500 py-2">{{ __('pages.dashboard.no_recent_activity') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@else
    <!-- AGENT / MEMBER -->
    @if($pendingFormsCount > 0)
        <div class="rounded-xl sm:rounded-2xl bg-white p-4 sm:p-6 shadow-sm border border-slate-100 min-w-0">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base sm:text-lg font-bold text-slate-900">{{ __('pages.dashboard.forms_to_fill') }}</h3>
                <a href="{{ route('forms.index') }}" class="text-sm font-semibold hover:underline w-fit" style="color: var(--accent);">{{ __('pages.dashboard.see_all_forms') }}</a>
            </div>
            <ul class="space-y-3">
                @foreach($pendingFormAssignments as $fa)
                    @php
                        $faStatus = $fa->status instanceof \App\Enums\FormAssignmentStatus ? $fa->status->value : (string) $fa->status;
                        $isOverdue = $faStatus === 'overdue';
                    @endphp
                    <li>
                        <a href="{{ route('forms.fill', $fa) }}" class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 bg-slate-50/30 hover:bg-slate-50/60 transition-colors shadow-sm group block">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $isOverdue ? 'bg-red-50 text-red-600' : 'bg-[var(--accent-soft)] text-[var(--accent)]' }}">
                                <iconify-icon icon="{{ $isOverdue ? 'solar:alarm-bold-duotone' : 'solar:clipboard-text-bold-duotone' }}" width="20"></iconify-icon>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-slate-900 truncate group-hover:text-[var(--accent)] transition-colors">{{ $fa->form?->name ?? '—' }}</p>
                                <div class="flex flex-wrap items-center gap-x-3 gap-y-0.5 mt-0.5 text-xs text-slate-500">
                                    @if($isOverdue)
                                        <span class="font-semibold text-red-600">{{ __('pages.dashboard.past_due') }}</span>
                                    @endif
                                    @if($fa->due_date)
                                        <span class="flex items-center gap-1">
                                            <iconify-icon icon="solar:calendar-linear" width="12"></iconify-icon>
                                            {{ __('pages.dashboard.due_date') }}: {{ $fa->due_date->format('d/m/Y') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium border shrink-0 {{ $isOverdue ? 'bg-red-50 text-red-700 border-red-100' : 'bg-[var(--accent-soft)] text-[var(--accent)] border-[var(--accent)]/20' }}">
                                {{ $isOverdue ? __('pages.dashboard.past_due') : __('pages.forms.status_pending') }}
                            </span>
                            <iconify-icon icon="solar:arrow-right-linear" width="18" class="text-slate-400 group-hover:text-[var(--accent)] transition-colors shrink-0"></iconify-icon>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-xl sm:rounded-2xl bg-white p-6 sm:p-8 lg:p-10 text-center shadow-sm border border-slate-100 min-w-0">
        <div class="mx-auto h-14 w-14 sm:h-16 sm:w-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
            <iconify-icon icon="solar:user-circle-bold" width="32" class="text-slate-400 w-7 h-7 sm:w-8 sm:h-8"></iconify-icon>
        </div>
        <h2 class="text-lg sm:text-xl font-bold text-slate-900">{{ __('pages.dashboard.welcome_space') }}</h2>
        <p class="mt-2 text-sm sm:text-base text-slate-500">{{ __('pages.dashboard.dashboard_loading') }}</p>
        <div class="mt-4 sm:mt-6">
            <a href="{{ route('tickets.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl px-5 py-3 text-sm font-semibold text-white shadow-lg hover:opacity-90 transition-all touch-target sm:min-h-0 w-full sm:w-auto" style="background-color: var(--accent);">
                <iconify-icon icon="solar:list-bold" width="18"></iconify-icon>
                {{ __('pages.dashboard.access_my_tickets') }}
            </a>
        </div>
    </div>
@endif
</div>
