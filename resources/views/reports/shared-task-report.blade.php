<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $orgName }} — {{ __('task_report.shared_report_title') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.iconify.design/iconify-icon/2.3.0/iconify-icon.min.js"></script>
</head>
<body class="bg-slate-50 min-h-screen">
    <div class="mx-auto max-w-5xl py-10 px-4 sm:px-6">

        {{-- Header --}}
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-900 text-white font-bold text-sm">
                    {{ mb_substr($orgName, 0, 1) }}
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ __('task_report.shared_report_title') }}</h1>
                    <p class="text-sm text-slate-500">{{ $orgName }} &middot; {{ __('task_report.shared_report_subtitle') }}</p>
                </div>
            </div>
            <p class="text-sm text-slate-500 mt-2">
                {{ __('task_report.period_label', ['from' => $from->format('d/m/Y'), 'to' => $to->format('d/m/Y')]) }}
            </p>
        </div>

        {{-- KPI Cards : principal = tickets clôturés --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            <div class="rounded-2xl bg-slate-900 p-6 text-white shadow-lg">
                <p class="text-sm font-medium text-white/70">{{ __('task_report.tickets_closed') }}</p>
                <h3 class="text-3xl font-bold mt-2">{{ number_format($stats['closedTicketsCount'] ?? 0, 0, ',', ' ') }}</h3>
                @if(($stats['tasksTotal'] ?? 0) > 0)
                    <p class="text-xs text-white/70 mt-1">{{ __('task_report.sub_tasks_also', ['count' => $stats['tasksTotal']]) }}</p>
                @endif
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-medium text-slate-500">{{ __('task_report.top_category') }}</p>
                <h3 class="text-xl font-bold text-slate-900 mt-1">{{ $stats['topCategoryClosed']['name'] ?? '—' }}</h3>
                <p class="text-xs text-slate-400 mt-1">{{ $stats['topCategoryClosed']['count'] ?? 0 }} {{ trans_choice('task_report.tickets_count', $stats['topCategoryClosed']['count'] ?? 0) }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-medium text-slate-500">{{ __('task_report.top_user') }}</p>
                <h3 class="text-xl font-bold text-slate-900 mt-1">{{ $stats['topUserClosed']['name'] ?? '—' }}</h3>
                <p class="text-xs text-slate-400 mt-1">{{ $stats['topUserClosed']['count'] ?? 0 }} {{ trans_choice('task_report.tickets_count', $stats['topUserClosed']['count'] ?? 0) }}</p>
            </div>
        </div>

        {{-- SECTION PRINCIPALE : Tickets clôturés --}}
        @if(isset($closedTickets) && $closedTickets->isNotEmpty())
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden mb-8">
                <div class="px-6 py-5 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-900">{{ __('task_report.closed_tickets') }}</h3>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $closedTickets->count() }} {{ trans_choice('task_report.tickets_count', $closedTickets->count()) }}</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500">
                            <tr>
                                <th class="px-6 py-3 text-left">{{ __('task_report.ticket') }}</th>
                                <th class="px-6 py-3 text-left">{{ __('task_report.category') }}</th>
                                <th class="px-6 py-3 text-left">{{ __('task_report.closed_at') }}</th>
                                <th class="px-6 py-3 text-left">{{ __('task_report.closed_by') }}</th>
                                <th class="px-6 py-3 text-left">{{ __('task_report.checklist_progress') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($closedTickets as $ticket)
                                @php
                                    $done = (int) ($ticket->checklist_done_count ?? 0);
                                    $total = (int) ($ticket->checklist_items_count ?? 0);
                                @endphp
                                <tr class="hover:bg-slate-50/50">
                                    <td class="px-6 py-3 text-sm font-medium text-slate-900">{{ Str::limit($ticket->subject, 50) }}</td>
                                    <td class="px-6 py-3 text-sm text-slate-600">{{ $ticket->category?->name ?? '—' }}</td>
                                    <td class="px-6 py-3 text-sm text-slate-500">{{ $ticket->updated_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-3 text-sm text-slate-600">{{ $ticket->closedByUser?->name ?? $ticket->assignees->first()?->name ?? $ticket->creator?->name ?? '—' }}</td>
                                    <td class="px-6 py-3 text-sm text-slate-600">{{ $total > 0 ? $done . '/' . $total : '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Footer --}}
        <div class="text-center text-xs text-slate-400 mt-8">
            <p>{{ __('task_report.shared_report_footer') }}</p>
        </div>
    </div>
</body>
</html>
