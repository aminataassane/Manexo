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
                @forelse ($this->priorityTickets as $t)
                    @php
                        $statusValue = is_object($t->status) ? $t->status->value : (string) $t->status;
                        $pill = $statusPill($statusValue);
                    @endphp
                    <tr class="group hover:bg-slate-50/80 transition-colors cursor-pointer" onclick="window.location='{{ route('tickets.discussion', $t) }}'">
                        <td class="px-4 sm:px-6 py-3 sm:py-4">
                            <div class="min-w-0">
                                <span class="inline-block text-[11px] font-mono font-medium text-slate-400 tracking-tight">{{ $t->shortReference() }}</span>
                                <p class="mt-0.5 text-sm font-semibold text-slate-900 group-hover:text-[var(--accent)] transition-colors truncate">{{ $t->subject }}</p>
                                <p class="mt-0.5 text-xs text-slate-500 line-clamp-1 max-w-[200px] sm:max-w-[280px]" title="{{ $t->description }}">{{ $t->description }}</p>
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
