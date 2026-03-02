<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1e293b; line-height: 1.5; }
        .header { padding: 20px 30px; border-bottom: 2px solid #e2e8f0; margin-bottom: 20px; }
        .header h1 { font-size: 18px; font-weight: bold; color: #0f172a; margin-bottom: 4px; }
        .header p { font-size: 10px; color: #64748b; }
        .content { padding: 0 30px; }
        .stats-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .stats-table td { padding: 12px 16px; border: 1px solid #e2e8f0; }
        .stats-table .stat-label { font-size: 10px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
        .stats-table .stat-value { font-size: 20px; font-weight: bold; color: #0f172a; }
        .section-title { font-size: 13px; font-weight: bold; color: #0f172a; margin: 20px 0 10px; padding-bottom: 6px; border-bottom: 1px solid #e2e8f0; }
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .data-table th { background: #f8fafc; padding: 8px 12px; text-align: left; font-size: 9px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0; }
        .data-table td { padding: 7px 12px; border-bottom: 1px solid #f1f5f9; font-size: 10px; color: #334155; }
        .data-table tr:nth-child(even) td { background: #f8fafc; }
        .footer { margin-top: 30px; padding: 15px 30px; border-top: 1px solid #e2e8f0; font-size: 9px; color: #94a3b8; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $orgName }} — {{ __('task_report.shared_report_title') }}</h1>
        <p>{{ __('task_report.period_label', ['from' => $from->format('d/m/Y'), 'to' => $to->format('d/m/Y')]) }} &middot; {{ __('task_report.report_generated', ['date' => now()->format('d/m/Y H:i')]) }}</p>
    </div>

    <div class="content">
        {{-- Stats summary : KPI principal = tickets clôturés --}}
        <table class="stats-table">
            <tr>
                <td style="width: 33%;">
                    <div class="stat-label">{{ __('task_report.tickets_closed') }}</div>
                    <div class="stat-value">{{ $stats['closedTicketsCount'] ?? 0 }}</div>
                </td>
                <td style="width: 33%;">
                    <div class="stat-label">{{ __('task_report.top_category') }}</div>
                    <div class="stat-value" style="font-size: 14px;">{{ $stats['topCategoryClosed']['name'] ?? '—' }}</div>
                </td>
                <td style="width: 33%;">
                    <div class="stat-label">{{ __('task_report.top_user') }}</div>
                    <div class="stat-value" style="font-size: 14px;">{{ $stats['topUserClosed']['name'] ?? '—' }}</div>
                </td>
            </tr>
        </table>

        {{-- SECTION PRINCIPALE : Tickets clôturés (avec nombre de sous-tâches) --}}
        @if(isset($closedTickets) && $closedTickets->isNotEmpty())
            <div class="section-title">{{ __('task_report.closed_tickets') }} ({{ $closedTickets->count() }})</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 35%;">{{ __('task_report.ticket') }}</th>
                        <th style="width: 18%;">{{ __('task_report.category') }}</th>
                        <th style="width: 18%;">{{ __('task_report.closed_at') }}</th>
                        <th style="width: 18%;">{{ __('task_report.closed_by') }}</th>
                        <th style="width: 11%;">{{ __('task_report.checklist_progress') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($closedTickets as $ticket)
                        @php
                            $done = (int) ($ticket->checklist_done_count ?? 0);
                            $total = (int) ($ticket->checklist_items_count ?? 0);
                        @endphp
                        <tr>
                            <td>{{ Str::limit($ticket->subject, 60) }}</td>
                            <td>{{ $ticket->category?->name ?? '—' }}</td>
                            <td>{{ $ticket->updated_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $ticket->closedByUser?->name ?? $ticket->assignees->first()?->name ?? $ticket->creator?->name ?? '—' }}</td>
                            <td>{{ $total > 0 ? $done . '/' . $total : '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="footer">
        {{ $orgName }} &middot; {{ __('task_report.report_generated', ['date' => now()->format('d/m/Y H:i')]) }}
    </div>
</body>
</html>
