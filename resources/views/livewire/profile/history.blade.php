@php
    $statusLabel = function (string $status): string {
        return __('tickets.status.' . $status);
    };
    $statusOrder = ['open', 'in_progress', 'pending', 'resolved', 'closed'];
    $statusPill = function (string $status): array {
        return match ($status) {
            'open' => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'border' => 'border-red-100'],
            'in_progress' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-100'],
            'pending' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-100'],
            'resolved' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-100'],
            'closed' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-700', 'border' => 'border-slate-200'],
            'soumis' => ['bg' => 'bg-violet-50', 'text' => 'text-violet-700', 'border' => 'border-violet-100'],
            default => ['bg' => 'bg-slate-50', 'text' => 'text-slate-600', 'border' => 'border-slate-200'],
        };
    };
@endphp

<div class="space-y-6 sm:space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ __('pages.history.heading') }}</h1>
            <p class="text-sm text-slate-500 mt-1">{{ __('pages.history.subheading') }}</p>
        </div>
        <a href="{{ route('profile') }}"
           class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
            <iconify-icon icon="solar:arrow-left-linear" width="18"></iconify-icon>
            {{ __('pages.history.back_to_profile') }}
        </a>
    </div>

    <!-- Stats cards -->
    <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-5 lg:gap-6">
        <div class="group relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 min-w-0">
            <div class="flex items-center justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-slate-500 truncate">{{ __('pages.history.stat_total_created') }}</p>
                    <p class="mt-1 sm:mt-2 text-2xl sm:text-3xl font-bold text-slate-900">{{ $stats['total_created'] }}</p>
                </div>
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600 group-hover:scale-105 transition-transform">
                    <iconify-icon icon="solar:document-add-bold-duotone" width="22"></iconify-icon>
                </div>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 min-w-0">
            <div class="flex items-center justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-slate-500 truncate">{{ __('pages.history.stat_total_assigned') }}</p>
                    <p class="mt-1 sm:mt-2 text-2xl sm:text-3xl font-bold text-slate-900">{{ $stats['total_assigned'] }}</p>
                </div>
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[var(--accent-soft)] text-[var(--accent)] group-hover:scale-105 transition-transform">
                    <iconify-icon icon="solar:user-check-bold-duotone" width="22"></iconify-icon>
                </div>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 min-w-0">
            <div class="flex items-center justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-slate-500 truncate">{{ __('pages.history.stat_total_forms') }}</p>
                    <p class="mt-1 sm:mt-2 text-2xl sm:text-3xl font-bold text-slate-900">{{ $stats['total_forms'] }}</p>
                </div>
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-violet-100 text-violet-600 group-hover:scale-105 transition-transform">
                    <iconify-icon icon="solar:clipboard-text-bold-duotone" width="22"></iconify-icon>
                </div>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 min-w-0">
            <div class="flex items-center justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-slate-500 truncate">{{ __('pages.history.stat_this_week') }}</p>
                    <p class="mt-1 sm:mt-2 text-2xl sm:text-3xl font-bold text-slate-900">{{ $stats['this_week'] }}</p>
                </div>
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600 group-hover:scale-105 transition-transform">
                    <iconify-icon icon="solar:calendar-bold-duotone" width="22"></iconify-icon>
                </div>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 min-w-0">
            <div class="flex items-center justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-slate-500 truncate">{{ __('pages.history.stat_this_month') }}</p>
                    <p class="mt-1 sm:mt-2 text-2xl sm:text-3xl font-bold text-slate-900">{{ $stats['this_month'] }}</p>
                </div>
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 group-hover:scale-105 transition-transform">
                    <iconify-icon icon="solar:calendar-minimalistic-bold-duotone" width="22"></iconify-icon>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
        <!-- Main: filters + timeline -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                    <h2 class="text-base font-semibold text-slate-900">{{ __('pages.profile.recent_activity') }}</h2>
                    <p class="text-xs text-slate-500 mt-0.5">{{ __('pages.profile.recent_activity_subtitle') }}</p>
                </div>
                <div class="p-4 sm:p-6">
                    <div class="flex flex-wrap gap-2 mb-6">
                        @php
                            $filters = [
                                ['all', __('pages.history.filter_all')],
                                ['tickets', __('pages.history.filter_tickets_created')],
                                ['assignations', __('pages.history.filter_assigned_to_me')],
                                ['forms', __('pages.history.filter_forms')],
                            ];
                        @endphp
                        @foreach ($filters as [$key, $label])
                            @php $isActive = $type === $key; @endphp
                            <button
                                type="button"
                                wire:click="setType('{{ $key }}')"
                                wire:loading.attr="disabled"
                                wire:target="setType"
                                class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $isActive ? 'bg-slate-900 text-white shadow-md' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 hover:text-slate-900' }}"
                            >{{ $label }}</button>
                        @endforeach
                    </div>

                    <div class="relative pl-4 space-y-6 before:absolute before:left-[19px] before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-100">
                        @forelse ($events as $e)
                            @php
                                $eventType = $e->event_type ?? 'tickets';
                                $statusValue = (string) ($e->status ?? '');
                                $pill = $statusPill($statusValue);
                                $label = $eventType === 'tickets' ? __('pages.history.label_ticket_created') : ($eventType === 'assignations' ? __('pages.history.label_ticket_assigned') : __('pages.history.label_form_submitted'));
                                $href = $e->ticket_id ? route('tickets.discussion', $e->ticket_id) : route('forms.index');
                            @endphp
                            <a href="{{ $href }}" wire:navigate.hover class="relative pl-6 block group">
                                <div class="absolute left-0 top-1.5 h-2.5 w-2.5 rounded-full border-2 border-white shadow-sm transition-transform group-hover:scale-110" style="background-color: var(--accent);"></div>
                                <p class="text-sm font-medium text-slate-900 group-hover:text-[var(--accent)] transition-colors">
                                    {{ $label }}
                                    @if ($e->ticket_id)
                                        <span class="text-slate-500 font-normal">· #{{ $e->ticket_id }}</span>
                                    @elseif ($e->form_response_id)
                                        <span class="text-slate-500 font-normal">· #{{ $e->form_response_id }}</span>
                                    @endif
                                </p>
                                <p class="text-sm text-slate-600 mt-0.5 truncate" title="{{ $e->subject }}">{{ $e->subject }}</p>
                                <div class="mt-2 flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border {{ $pill['bg'] }} {{ $pill['text'] }} {{ $pill['border'] }}">
                                        {{ $statusValue === 'soumis' ? __('pages.forms.status_submitted') : $statusLabel($statusValue) }}
                                    </span>
                                    <span class="text-xs text-slate-400">{{ \Illuminate\Support\Carbon::parse($e->at)->diffForHumans() }}</span>
                                </div>
                            </a>
                        @empty
                            <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50 p-6 text-center text-sm text-slate-500">
                                <iconify-icon icon="solar:history-linear" width="32" class="mx-auto mb-2 opacity-50"></iconify-icon>
                                {{ __('pages.history.no_events') }}
                            </div>
                        @endforelse
                    </div>

                    @if ($events->hasPages())
                        <div class="mt-6 pt-4 border-t border-slate-100">
                            {{ $events->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar: by status -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                    <h2 class="text-base font-semibold text-slate-900">{{ __('pages.history.stat_by_status') }}</h2>
                    <p class="text-xs text-slate-500 mt-0.5">{{ __('pages.history.stat_total_created') }} + {{ __('pages.history.stat_total_assigned') }} + {{ __('pages.history.stat_total_forms') }}</p>
                </div>
                <div class="p-4 sm:p-6">
                    <div class="space-y-3">
                        @foreach ($statusOrder as $s)
                            @if (isset($stats['by_status'][$s]))
                                @php $pill = $statusPill($s); @endphp
                                <div class="flex items-center justify-between gap-3 rounded-xl border border-slate-100 px-3 py-2.5 hover:bg-slate-50/50 transition-colors">
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg text-xs font-medium {{ $pill['bg'] }} {{ $pill['text'] }} {{ $pill['border'] }} border">
                                        {{ $statusLabel($s) }}
                                    </span>
                                    <span class="text-sm font-bold text-slate-900">{{ $stats['by_status'][$s] }}</span>
                                </div>
                            @endif
                        @endforeach
                        @php $otherStatuses = array_diff_key($stats['by_status'], array_flip($statusOrder)); @endphp
                        @foreach ($otherStatuses as $s => $cnt)
                            @php $pill = $statusPill($s); @endphp
                            <div class="flex items-center justify-between gap-3 rounded-xl border border-slate-100 px-3 py-2.5 hover:bg-slate-50/50 transition-colors">
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg text-xs font-medium {{ $pill['bg'] }} {{ $pill['text'] }} {{ $pill['border'] }} border">
                                    {{ $statusLabel($s) }}
                                </span>
                                <span class="text-sm font-bold text-slate-900">{{ $cnt }}</span>
                            </div>
                        @endforeach
                        @if (empty($stats['by_status']))
                            <p class="text-sm text-slate-500 py-4 text-center">{{ __('pages.history.no_events') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
