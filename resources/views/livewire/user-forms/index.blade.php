@php
    $statusLabels = [
        'submitted' => __('pages.forms.status_submitted'),
        'overdue' => __('pages.forms.status_overdue'),
        'pending' => __('pages.forms.status_pending'),
        'expired' => __('pages.forms.status_expired'),
    ];
@endphp

<div class="section-responsive min-w-0 pb-6 sm:pb-8" style="padding-bottom: max(1.5rem, env(safe-area-inset-bottom, 0));">
    {{-- En-tête --}}
    <div class="flex flex-col gap-4 sm:gap-6 mb-6 sm:mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
            <div class="min-w-0">
                <div class="flex items-center gap-3 mb-1">
                    <div class="flex h-10 w-10 sm:h-12 sm:w-12 shrink-0 items-center justify-center rounded-xl shadow-sm" style="background: var(--accent-soft); color: var(--accent);">
                        <iconify-icon icon="solar:clipboard-text-bold-duotone" width="24" class="sm:w-7 sm:h-7"></iconify-icon>
                    </div>
                    <div class="min-w-0">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight sm:text-2xl truncate">{{ __('pages.forms.my_forms') }}</h1>
                        <p class="text-sm text-slate-500 mt-0.5">{{ __('pages.forms.subtitle') }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if(session('form_success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800 flex items-center gap-3 shadow-sm">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600">
                    <iconify-icon icon="solar:check-circle-bold" width="18"></iconify-icon>
                </div>
                <span>{{ session('form_success') }}</span>
            </div>
        @endif

        {{-- Statistiques --}}
        @php
            $stats = $this->formStats;
        @endphp
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
            <div class="rounded-xl border border-slate-100 bg-white p-4 shadow-sm min-w-0">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-[var(--accent-soft)] text-[var(--accent)]">
                        <iconify-icon icon="solar:clipboard-text-linear" width="20"></iconify-icon>
                    </div>
                    <div class="min-w-0">
                        <p class="text-2xl font-bold text-slate-900 tabular-nums">{{ $stats['pending'] + $stats['overdue'] }}</p>
                        <p class="text-xs font-medium text-slate-500 truncate">{{ __('pages.forms.stat_to_fill') }}</p>
                    </div>
                </div>
            </div>
            <div class="rounded-xl border border-red-100 bg-red-50/50 p-4 shadow-sm min-w-0">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-red-100 text-red-600">
                        <iconify-icon icon="solar:alarm-linear" width="20"></iconify-icon>
                    </div>
                    <div class="min-w-0">
                        <p class="text-2xl font-bold text-red-700 tabular-nums">{{ $stats['overdue'] }}</p>
                        <p class="text-xs font-medium text-slate-500 truncate">{{ __('pages.forms.stat_overdue') }}</p>
                    </div>
                </div>
            </div>
            <div class="rounded-xl border border-emerald-100 bg-emerald-50/50 p-4 shadow-sm min-w-0">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600">
                        <iconify-icon icon="solar:check-circle-linear" width="20"></iconify-icon>
                    </div>
                    <div class="min-w-0">
                        <p class="text-2xl font-bold text-emerald-700 tabular-nums">{{ $stats['submitted'] }}</p>
                        <p class="text-xs font-medium text-slate-500 truncate">{{ __('pages.forms.stat_submitted') }}</p>
                    </div>
                </div>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-4 shadow-sm min-w-0">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-200 text-slate-500">
                        <iconify-icon icon="solar:lock-keyhole-linear" width="20"></iconify-icon>
                    </div>
                    <div class="min-w-0">
                        <p class="text-2xl font-bold text-slate-600 tabular-nums">{{ $stats['expired'] }}</p>
                        <p class="text-xs font-medium text-slate-500 truncate">{{ __('pages.forms.stat_expired') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Onglets (style aligné Discussions / Dashboard) --}}
        <div class="flex rounded-xl bg-slate-100 p-1 w-full max-w-full sm:w-fit">
            <button type="button" wire:click="$set('tab', 'pending')"
                    class="flex-1 sm:flex-none flex items-center justify-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-2.5 sm:py-2 text-xs sm:text-[13px] font-bold rounded-lg transition-all min-h-[40px] sm:min-h-0 touch-manipulation {{ $tab === 'pending' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900' }}">
                <iconify-icon icon="solar:clipboard-text-linear" width="16"></iconify-icon>
                <span>{{ __('pages.forms.to_fill') }}</span>
            </button>
            <button type="button" wire:click="$set('tab', 'submitted')"
                    class="flex-1 sm:flex-none flex items-center justify-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-2.5 sm:py-2 text-xs sm:text-[13px] font-bold rounded-lg transition-all min-h-[40px] sm:min-h-0 touch-manipulation {{ $tab === 'submitted' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900' }}">
                <iconify-icon icon="solar:check-read-linear" width="16"></iconify-icon>
                <span>{{ __('pages.forms.submitted') }}</span>
            </button>
            <button type="button" wire:click="$set('tab', 'all')"
                    class="flex-1 sm:flex-none flex items-center justify-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-2.5 sm:py-2 text-xs sm:text-[13px] font-bold rounded-lg transition-all min-h-[40px] sm:min-h-0 touch-manipulation {{ $tab === 'all' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900' }}">
                <iconify-icon icon="solar:list-linear" width="16"></iconify-icon>
                <span>{{ __('pages.forms.all') }}</span>
            </button>
        </div>
    </div>

    {{-- Formulaires équipe (tous peuvent répondre) --}}
    @if($this->teamForms->isNotEmpty())
        <div class="mb-6 sm:mb-8">
            <h2 class="text-sm font-bold text-slate-800 mb-2">{{ __('pages.forms.team_forms_title') }}</h2>
            <p class="text-xs text-slate-500 mb-3">{{ __('pages.forms.team_forms_help') }}</p>
            <ul class="space-y-3">
                @foreach($this->teamForms as $teamForm)
                    <li class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-4 rounded-xl border border-slate-100 bg-slate-50/30 sm:bg-white shadow-sm hover:shadow-md hover:bg-slate-50/50 sm:hover:bg-slate-50/30 transition-all min-w-0">
                        <div class="min-w-0 flex-1">
                            <h3 class="text-sm font-bold text-slate-900 truncate">{{ $teamForm->name }}</h3>
                            @if($teamForm->description)
                                <p class="text-xs text-slate-500 mt-0.5 line-clamp-2">{{ $teamForm->description }}</p>
                            @endif
                            @if($teamForm->slug)
                                <p class="text-[11px] text-slate-400 mt-1.5 font-mono break-all">{{ url('/forms/l/' . $teamForm->slug) }}</p>
                            @endif
                        </div>
                        <div class="flex flex-wrap items-center gap-2 shrink-0">
                            @if($teamForm->slug)
                                <a href="{{ route('forms.fill-team-by-slug', $teamForm->slug) }}"
                                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 sm:py-2 text-xs font-bold text-white rounded-xl shadow-sm hover:opacity-90 transition-all touch-manipulation"
                                   style="background-color: var(--accent);">
                                    <iconify-icon icon="solar:pen-bold" width="14"></iconify-icon>
                                    {{ __('pages.forms.fill') }}
                                </a>
                            @else
                                <a href="{{ route('forms.fill-team', $teamForm) }}"
                                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 sm:py-2 text-xs font-bold text-white rounded-xl shadow-sm hover:opacity-90 transition-all touch-manipulation"
                                   style="background-color: var(--accent);">
                                    <iconify-icon icon="solar:pen-bold" width="14"></iconify-icon>
                                    {{ __('pages.forms.fill') }}
                                </a>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Liste des assignations --}}
    <div class="space-y-3">
        @if($this->teamForms->isNotEmpty())
            <h2 class="text-sm font-bold text-slate-800 mb-2">{{ __('pages.forms.assigned_to_you') }}</h2>
        @endif
        @forelse($this->assignments as $a)
            @php
                $aStatus = $a->status instanceof \App\Enums\FormAssignmentStatus ? $a->status->value : (string) $a->status;
                $aBadge = match($aStatus) {
                    'submitted' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-100', 'label' => $statusLabels['submitted'] ?? __('pages.forms.status_submitted'), 'icon' => 'solar:check-circle-bold'],
                    'overdue' => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'border' => 'border-red-100', 'label' => $statusLabels['overdue'] ?? __('pages.forms.status_overdue'), 'icon' => 'solar:alarm-bold'],
                    'expired' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-500', 'border' => 'border-slate-200', 'label' => $statusLabels['expired'] ?? __('pages.forms.status_expired'), 'icon' => 'solar:lock-keyhole-bold'],
                    default => ['bg' => 'bg-[var(--accent-soft)]', 'text' => 'text-[var(--accent)]', 'border' => 'border-[var(--accent)]/20', 'label' => $statusLabels['pending'] ?? __('pages.forms.status_pending'), 'icon' => 'solar:clock-circle-bold'],
                };
            @endphp
            <article class="rounded-xl sm:rounded-2xl border border-slate-100 bg-slate-50/30 sm:bg-white shadow-sm hover:shadow-md transition-all duration-200 min-w-0 overflow-hidden">
                <div class="flex flex-col sm:flex-row sm:items-center gap-4 p-4 sm:p-5">
                    <div class="flex items-start gap-3 sm:gap-4 min-w-0 flex-1">
                        <div class="flex h-10 w-10 sm:h-11 sm:w-11 shrink-0 items-center justify-center rounded-xl transition-transform duration-200 sm:group-hover:scale-105 {{ $aStatus === 'overdue' ? 'bg-red-50 text-red-600' : ($aStatus === 'submitted' ? 'bg-emerald-50 text-emerald-600' : ($aStatus === 'expired' ? 'bg-slate-100 text-slate-500' : 'bg-[var(--accent-soft)] text-[var(--accent)]')) }}">
                            @if($aStatus === 'submitted')
                                <iconify-icon icon="solar:check-circle-bold-duotone" width="20"></iconify-icon>
                            @elseif($aStatus === 'overdue')
                                <iconify-icon icon="solar:alarm-bold-duotone" width="20"></iconify-icon>
                            @elseif($aStatus === 'expired')
                                <iconify-icon icon="solar:lock-keyhole-bold-duotone" width="20"></iconify-icon>
                            @else
                                <iconify-icon icon="solar:clipboard-text-bold-duotone" width="20"></iconify-icon>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            @if(!in_array($aStatus, ['submitted', 'expired']))
                                <a href="{{ route('forms.fill', $a->id) }}" class="group block">
                            @endif
                            <h3 class="text-sm sm:text-base font-bold text-slate-900 truncate {{ !in_array($aStatus, ['submitted', 'expired']) ? 'group-hover:text-[var(--accent)] transition-colors' : '' }}">{{ $a->form?->name ?? '—' }}</h3>
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1 text-xs text-slate-500">
                                <span class="flex items-center gap-1">
                                    <iconify-icon icon="solar:user-linear" width="12"></iconify-icon>
                                    {{ __('pages.forms.by') }} {{ $a->assignedBy?->name ?? '—' }}
                                </span>
                                @if($a->due_date)
                                    <span class="flex items-center gap-1 {{ $aStatus === 'overdue' ? 'text-red-600 font-medium' : '' }}">
                                        <iconify-icon icon="solar:calendar-linear" width="12"></iconify-icon>
                                        {{ __('pages.forms.due_date') }}: {{ $a->due_date->format('d/m/Y') }}
                                    </span>
                                @endif
                                @if($a->expires_at)
                                    <span class="flex items-center gap-1 {{ $aStatus === 'expired' ? 'text-slate-500 font-medium' : '' }}">
                                        <iconify-icon icon="solar:lock-keyhole-linear" width="12"></iconify-icon>
                                        {{ __('pages.forms.expires_at') }}: {{ $a->expires_at->format('d/m/Y H:i') }}
                                    </span>
                                @endif
                            </div>
                            @if(!in_array($aStatus, ['submitted', 'expired']))
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 sm:gap-3 shrink-0 sm:pl-4 border-t border-slate-100 sm:border-t-0 sm:border-l border-slate-100 pt-3 sm:pt-0 sm:pl-4">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-semibold border {{ $aBadge['bg'] }} {{ $aBadge['text'] }} {{ $aBadge['border'] }}">
                            <iconify-icon icon="{{ $aBadge['icon'] }}" width="12"></iconify-icon>
                            {{ $aBadge['label'] }}
                        </span>
                        @if(!in_array($aStatus, ['submitted', 'expired']))
                            <a href="{{ route('forms.fill', $a->id) }}"
                               class="inline-flex items-center justify-center gap-2 px-4 py-2.5 sm:py-2 text-xs font-bold text-white rounded-xl shadow-sm hover:opacity-90 transition-all touch-manipulation min-h-[40px] sm:min-h-0"
                               style="background-color: var(--accent);">
                                <iconify-icon icon="solar:pen-bold" width="14"></iconify-icon>
                                {{ __('pages.forms.fill') }}
                            </a>
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-xl sm:rounded-2xl border border-slate-100 bg-white p-8 sm:p-10 lg:p-12 text-center shadow-sm min-w-0">
                <div class="mx-auto flex h-16 w-16 sm:h-20 sm:w-20 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 mb-5">
                    <iconify-icon icon="solar:clipboard-check-linear" width="36" class="sm:w-10 sm:h-10"></iconify-icon>
                </div>
                <h3 class="text-base sm:text-lg font-bold text-slate-900">{{ __('pages.forms.no_forms') }}</h3>
                <p class="mt-2 text-sm text-slate-500 max-w-sm mx-auto">
                    @if($tab === 'pending')
                        {{ __('pages.forms.no_forms_pending') }}
                    @elseif($tab === 'submitted')
                        {{ __('pages.forms.no_forms_submitted') }}
                    @else
                        {{ __('pages.forms.no_forms_assigned') }}
                    @endif
                </p>
                @if($tab !== 'all')
                    <button type="button" wire:click="$set('tab', 'all')"
                            class="mt-5 inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
                        <iconify-icon icon="solar:list-linear" width="16"></iconify-icon>
                        {{ __('pages.forms.see_all') }}
                    </button>
                @endif
            </div>
        @endforelse
    </div>
</div>
