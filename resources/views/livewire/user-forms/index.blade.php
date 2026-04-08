@php
    $statusLabels = [
        'submitted' => __('pages.forms.status_submitted'),
        'overdue'   => __('pages.forms.status_overdue'),
        'pending'   => __('pages.forms.status_pending'),
        'expired'   => __('pages.forms.status_expired'),
    ];
@endphp

<div class="w-full max-w-full min-w-0 mx-auto">

    {{-- Header --}}
    <div class="page-header">
        <div class="min-w-0">
            <h1 class="page-title">{{ __('pages.forms.my_forms') }}</h1>
            <p class="page-subtitle">{{ __('pages.forms.subtitle') }}</p>
        </div>
    </div>

    {{-- Alert --}}
    @if(session('form_success'))
        <div class="flex items-center gap-3 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800 mb-6">
            <iconify-icon icon="solar:check-circle-bold" width="18" class="text-emerald-500 shrink-0"></iconify-icon>
            <span>{{ session('form_success') }}</span>
        </div>
    @endif

    @php $stats = $this->formStats; @endphp

    {{-- Navigation : tabs + compteurs inline --}}
    <div class="mb-6 sm:mb-8">
        <div class="flex items-center gap-1 overflow-x-auto pb-px">
            @php
                $tabs = [
                    ['key' => 'pending',   'label' => __('pages.forms.to_fill'),       'count' => $stats['pending'],   'urgent' => false],
                    ['key' => 'overdue',   'label' => __('pages.forms.stat_overdue'),   'count' => $stats['overdue'],   'urgent' => true],
                    ['key' => 'submitted', 'label' => __('pages.forms.submitted'),      'count' => $stats['submitted'], 'urgent' => false],
                    ['key' => 'expired',   'label' => __('pages.forms.stat_expired'),   'count' => $stats['expired'],   'urgent' => false],
                    ['key' => 'all',       'label' => __('pages.forms.all'),            'count' => $stats['total'],     'urgent' => false],
                ];
            @endphp
            @foreach($tabs as $t)
                <button
                    type="button"
                    wire:click="setTab('{{ $t['key'] }}')"
                    wire:loading.attr="disabled"
                    wire:target="setTab"
                    class="relative px-4 py-2.5 text-sm font-medium whitespace-nowrap transition-colors {{ $tab === $t['key'] ? 'text-slate-900' : 'text-slate-500 hover:text-slate-700' }}"
                >
                    {{ $t['label'] }}
                    @if($t['count'] > 0)
                        <span class="ml-1.5 text-xs tabular-nums {{ $tab === $t['key'] ? ($t['urgent'] ? 'text-red-600 font-semibold' : 'text-slate-900 font-semibold') : ($t['urgent'] && $t['count'] > 0 ? 'text-red-500' : 'text-slate-400') }}">{{ $t['count'] }}</span>
                    @endif
                    @if($tab === $t['key'])
                        <span class="absolute bottom-0 left-4 right-4 h-0.5 rounded-full" style="background: var(--accent);"></span>
                    @endif
                </button>
            @endforeach
        </div>
        <div class="h-px bg-slate-200"></div>
    </div>

    {{-- Formulaires d'équipe --}}
    @if(in_array($tab, ['pending', 'all'], true) && $this->teamForms->isNotEmpty())
        <div class="mb-8">
            <h2 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4">{{ __('pages.forms.team_forms_title') }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
                @foreach($this->teamForms as $teamForm)
                    <a href="{{ $teamForm->slug ? route('forms.fill-team-by-slug', $teamForm->slug) : route('forms.fill-team', $teamForm) }}"
                       wire:navigate.hover
                       class="group flex items-center gap-3.5 rounded-xl border border-slate-200 bg-white px-4 py-3.5 hover:border-slate-300 hover:shadow-sm transition-all">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-violet-50 text-violet-500">
                            <iconify-icon icon="solar:users-group-rounded-bold" width="16"></iconify-icon>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-semibold text-slate-900 truncate group-hover:text-[var(--accent)] transition-colors">{{ $teamForm->name }}</div>
                            @if($teamForm->description)
                                <div class="text-xs text-slate-500 truncate mt-0.5">{{ $teamForm->description }}</div>
                            @endif
                        </div>
                        <iconify-icon icon="solar:arrow-right-linear" width="16" class="text-slate-300 group-hover:text-[var(--accent)] shrink-0 transition-colors"></iconify-icon>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Liste des formulaires assignés --}}
    @if(in_array($tab, ['pending', 'all'], true) && $this->teamForms->isNotEmpty())
        <h2 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4">{{ __('pages.forms.assigned_to_you') }}</h2>
    @endif

    <div class="rounded-xl border border-slate-200 bg-white overflow-hidden">
        @forelse($this->assignments as $a)
            @php
                $aStatus = $a->status instanceof \App\Enums\FormAssignmentStatus ? $a->status->value : (string) $a->status;
                $isActionable = !in_array($aStatus, ['submitted', 'expired']);
                $isOverdue = $aStatus === 'overdue';
                $isSubmitted = $aStatus === 'submitted';
                $isExpired = $aStatus === 'expired';
            @endphp
            <div class="flex items-center gap-4 px-5 py-4 {{ !$loop->last ? 'border-b border-slate-100' : '' }} {{ $isExpired ? 'opacity-50' : '' }} {{ $isOverdue ? 'bg-red-50/30' : '' }}">
                {{-- Status dot --}}
                <div class="shrink-0">
                    @if($isOverdue)
                        <div class="h-2.5 w-2.5 rounded-full bg-red-500 ring-4 ring-red-50"></div>
                    @elseif($isSubmitted)
                        <div class="h-2.5 w-2.5 rounded-full bg-emerald-500 ring-4 ring-emerald-50"></div>
                    @elseif($isExpired)
                        <div class="h-2.5 w-2.5 rounded-full bg-slate-300"></div>
                    @else
                        <div class="h-2.5 w-2.5 rounded-full ring-4 ring-[var(--accent-soft)]" style="background: var(--accent);"></div>
                    @endif
                </div>

                {{-- Form info --}}
                <div class="min-w-0 flex-1">
                    @if($isActionable)
                        <a href="{{ route('forms.fill', $a) }}" class="text-sm font-semibold text-slate-900 hover:text-[var(--accent)] transition-colors truncate block">{{ $a->form?->name ?? '—' }}</a>
                    @else
                        <span class="text-sm font-semibold {{ $isExpired ? 'text-slate-400' : 'text-slate-900' }} truncate block">{{ $a->form?->name ?? '—' }}</span>
                    @endif
                    <div class="flex items-center gap-3 mt-1 text-xs text-slate-500">
                        <span>{{ $a->assignedBy?->name ?? '—' }}</span>
                        @if($a->due_date)
                            <span class="{{ $isOverdue ? 'text-red-600 font-medium' : '' }}">{{ $a->due_date->format('d/m/Y') }}</span>
                        @endif
                    </div>
                </div>

                {{-- Status badge --}}
                <div class="shrink-0 hidden sm:block">
                    @if($isOverdue)
                        <span class="text-xs font-medium text-red-600">{{ $statusLabels['overdue'] }}</span>
                    @elseif($isSubmitted)
                        <span class="text-xs font-medium text-emerald-600">{{ $statusLabels['submitted'] }}</span>
                    @elseif($isExpired)
                        <span class="text-xs text-slate-400">{{ $statusLabels['expired'] }}</span>
                    @else
                        <span class="text-xs text-slate-500">{{ $statusLabels['pending'] }}</span>
                    @endif
                </div>

                {{-- Action --}}
                @if($isActionable)
                    <a href="{{ route('forms.fill', $a) }}"
                       wire:navigate.hover
                       class="shrink-0 inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors text-white hover:opacity-90"
                       style="background: var(--accent);">
                        {{ __('pages.forms.fill') }}
                    </a>
                @endif
            </div>
        @empty
            <div class="py-16 text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-400 mb-4">
                    @if($tab === 'submitted')
                        <iconify-icon icon="solar:check-read-linear" width="24"></iconify-icon>
                    @elseif($tab === 'overdue')
                        <iconify-icon icon="solar:alarm-linear" width="24"></iconify-icon>
                    @elseif($tab === 'expired')
                        <iconify-icon icon="solar:lock-keyhole-linear" width="24"></iconify-icon>
                    @else
                        <iconify-icon icon="solar:clipboard-check-linear" width="24"></iconify-icon>
                    @endif
                </div>
                <p class="text-sm font-semibold text-slate-700">{{ __('pages.forms.no_forms') }}</p>
                <p class="text-xs text-slate-500 mt-1 max-w-xs mx-auto">
                    @if($tab === 'pending')
                        {{ __('pages.forms.no_forms_pending') }}
                    @elseif($tab === 'submitted')
                        {{ __('pages.forms.no_forms_submitted') }}
                    @elseif($tab === 'overdue')
                        {{ __('pages.forms.no_forms_overdue') }}
                    @elseif($tab === 'expired')
                        {{ __('pages.forms.no_forms_expired') }}
                    @else
                        {{ __('pages.forms.no_forms_assigned') }}
                    @endif
                </p>
                @if($tab !== 'all')
                    <button type="button" wire:click="setTab('all')" wire:loading.attr="disabled" wire:target="setTab" class="mt-4 text-xs font-medium text-slate-500 hover:text-slate-900 transition-colors underline underline-offset-2">
                        {{ __('pages.forms.see_all') }}
                    </button>
                @endif
            </div>
        @endforelse
    </div>
</div>
