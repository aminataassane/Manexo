<div class="rounded-xl sm:rounded-2xl bg-white shadow-sm border border-slate-100 overflow-hidden min-w-0">
    <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-slate-100 flex items-center justify-between">
        <h3 class="text-base sm:text-lg font-bold text-slate-900">{{ __('pages.dashboard.my_recent_tickets') }}</h3>
        <a href="{{ route('tickets.index') }}" class="text-sm font-semibold hover:underline" style="color: var(--accent);" wire:navigate.hover>{{ __('pages.dashboard.see_all') }}</a>
    </div>
    <div class="responsive-table-wrap">
        <table class="w-full text-left min-w-[500px] sm:min-w-0">
            <thead class="bg-slate-50/50 text-xs uppercase text-slate-500 font-semibold">
                <tr>
                    <th class="px-4 sm:px-6 py-3">{{ __('pages.dashboard.subject') }}</th>
                    <th class="px-4 sm:px-6 py-3">{{ __('pages.dashboard.status') }}</th>
                    <th class="px-4 sm:px-6 py-3 text-right">{{ __('pages.dashboard.last_activity') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($this->myRecentTickets as $t)
                    @php
                        $statusValue = is_object($t->status) ? $t->status->value : (string) $t->status;
                        $pill = $statusPill($statusValue);
                    @endphp
                    <tr class="group hover:bg-slate-50/80 transition-colors cursor-pointer" onclick="window.location='{{ route('tickets.discussion', $t) }}'">
                        <td class="px-4 sm:px-6 py-3 sm:py-4">
                            <div class="min-w-0">
                                <span class="inline-block text-[11px] font-mono font-medium text-slate-400 tracking-tight">{{ $t->shortReference() }}</span>
                                <p class="mt-0.5 text-sm font-semibold text-slate-900 group-hover:text-[var(--accent)] transition-colors truncate max-w-[280px]">{{ $t->subject }}</p>
                            </div>
                        </td>
                        <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border {{ $pill['bg'] }} {{ $pill['text'] }} {{ $pill['border'] }}">
                                {{ $statusLabel($statusValue) }}
                            </span>
                        </td>
                        <td class="px-4 sm:px-6 py-3 sm:py-4 text-right text-sm text-slate-500 whitespace-nowrap">{{ $t->updated_at?->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 sm:px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mb-3">
                                    <iconify-icon icon="solar:ticket-linear" width="28" class="text-slate-300"></iconify-icon>
                                </div>
                                <p class="text-sm font-semibold text-slate-700">{{ __('pages.dashboard.no_tickets_yet') }}</p>
                                <p class="text-xs text-slate-500 mt-1">{{ __('pages.dashboard.no_tickets_hint') }}</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
