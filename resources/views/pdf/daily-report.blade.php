<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #1e293b; line-height: 1.5; }
        .header { padding: 20px 30px; border-bottom: 3px solid #0f172a; margin-bottom: 20px; }
        .header h1 { font-size: 18px; font-weight: bold; color: #0f172a; margin-bottom: 2px; }
        .header p { font-size: 9px; color: #64748b; }
        .content { padding: 0 30px; }

        .kpi-table { width: 100%; margin-bottom: 18px; border-collapse: collapse; }
        .kpi-table td { padding: 10px 14px; border: 1px solid #e2e8f0; vertical-align: top; }
        .kpi-label { font-size: 8px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; }
        .kpi-value { font-size: 18px; font-weight: bold; color: #0f172a; margin-top: 2px; }
        .kpi-accent td:first-child { background: #0f172a; color: #fff; }
        .kpi-accent td:first-child .kpi-label { color: #94a3b8; }
        .kpi-accent td:first-child .kpi-value { color: #fff; }

        .section-title { font-size: 12px; font-weight: bold; color: #0f172a; margin: 18px 0 8px; padding-bottom: 5px; border-bottom: 1px solid #e2e8f0; }
        .section-title span { font-weight: normal; font-size: 10px; color: #64748b; }

        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        .data-table th { background: #f8fafc; padding: 6px 10px; text-align: left; font-size: 8px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0; }
        .data-table td { padding: 5px 10px; border-bottom: 1px solid #f1f5f9; font-size: 9px; color: #334155; }
        .data-table tr:nth-child(even) td { background: #fafbfc; }

        .badge { display: inline-block; padding: 1px 6px; border-radius: 3px; font-size: 8px; font-weight: bold; }
        .badge-red { background: #fef2f2; color: #dc2626; }
        .badge-amber { background: #fffbeb; color: #d97706; }
        .badge-blue { background: #eff6ff; color: #2563eb; }
        .badge-slate { background: #f1f5f9; color: #475569; }

        .backlog-table { width: 100%; margin-bottom: 18px; border-collapse: collapse; }
        .backlog-table td { padding: 8px 14px; border: 1px solid #e2e8f0; text-align: center; width: 33.33%; }
        .backlog-label { font-size: 8px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; }
        .backlog-value { font-size: 16px; font-weight: bold; color: #0f172a; }

        .agent-table { width: 50%; }
        .two-col { width: 100%; }
        .two-col td { vertical-align: top; padding: 0; }
        .two-col td:first-child { padding-right: 12px; width: 55%; }
        .two-col td:last-child { padding-left: 12px; width: 45%; }

        .footer { margin-top: 20px; padding: 12px 30px; border-top: 1px solid #e2e8f0; font-size: 8px; color: #94a3b8; text-align: center; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-mono { font-family: 'DejaVu Sans Mono', monospace; }
        .text-bold { font-weight: bold; }
        .text-red { color: #dc2626; }
        .text-amber { color: #d97706; }
    </style>
</head>
<body>
    {{-- ─── HEADER ─────────────────────────────────── --}}
    <div class="header">
        <h1>{{ $orgName }} &mdash; {{ __('daily_report.title') }}</h1>
        <p>{{ __('daily_report.export_date') }} : {{ \Illuminate\Support\Carbon::parse($date)->translatedFormat('l j F Y') }} &middot; {{ __('task_report.report_generated', ['date' => now()->format('d/m/Y H:i')]) }}</p>
    </div>

    <div class="content">
        {{-- ─── KPIs ───────────────────────────────── --}}
        <table class="kpi-table kpi-accent">
            <tr>
                <td style="width:25%">
                    <div class="kpi-label">{{ __('daily_report.created_today') }}</div>
                    <div class="kpi-value">{{ $summary['created_total'] }}</div>
                </td>
                <td style="width:25%">
                    <div class="kpi-label">{{ __('daily_report.resolved_today') }}</div>
                    <div class="kpi-value">{{ $summary['resolved_today'] }}</div>
                </td>
                <td style="width:25%">
                    <div class="kpi-label">{{ __('daily_report.avg_first_response') }}</div>
                    <div class="kpi-value" style="font-size:14px">{{ $formatDuration($summary['avg_first_response_seconds']) }}</div>
                </td>
                <td style="width:25%">
                    <div class="kpi-label">{{ __('daily_report.avg_resolution_time') }}</div>
                    <div class="kpi-value" style="font-size:14px">{{ $formatDuration($summary['avg_resolution_seconds']) }}</div>
                </td>
            </tr>
        </table>

        {{-- ─── BACKLOG ────────────────────────────── --}}
        <table class="backlog-table">
            <tr>
                <td>
                    <div class="backlog-label">{{ __('daily_report.open') }}</div>
                    <div class="backlog-value">{{ $summary['backlog_open'] }}</div>
                </td>
                <td>
                    <div class="backlog-label">{{ __('daily_report.in_progress') }}</div>
                    <div class="backlog-value">{{ $summary['backlog_in_progress'] }}</div>
                </td>
                <td>
                    <div class="backlog-label">{{ __('daily_report.pending') }}</div>
                    <div class="backlog-value">{{ $summary['backlog_pending'] }}</div>
                </td>
            </tr>
        </table>

        {{-- ─── TICKETS A RISQUE ───────────────────── --}}
        @php $riskCount = $atRisk['overdue']->count() + $atRisk['due_soon']->count(); @endphp
        @if($riskCount > 0)
            <div class="section-title">{{ __('daily_report.at_risk') }} <span>({{ $riskCount }})</span></div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:10%">{{ __('daily_report.status') }}</th>
                        <th style="width:10%">{{ __('daily_report.reference') }}</th>
                        <th style="width:25%">{{ __('daily_report.subject') }}</th>
                        <th style="width:12%">{{ __('daily_report.group') }}</th>
                        <th style="width:10%">{{ __('daily_report.priority') }}</th>
                        <th style="width:18%">{{ __('daily_report.assignees') }}</th>
                        <th style="width:10%">{{ __('daily_report.due_date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($atRisk['overdue'] as $ticket)
                        <tr>
                            <td><span class="badge badge-red">{{ __('daily_report.overdue') }}</span></td>
                            <td class="font-mono">{{ $ticket->public_id }}</td>
                            <td class="text-bold">{{ Str::limit($ticket->subject, 50) }}</td>
                            <td>{{ $ticket->group?->name ?? '—' }}</td>
                            <td>{{ $ticket->priority?->name ?? '—' }}</td>
                            <td>{{ $ticket->assignees->pluck('name')->join(', ') ?: __('daily_report.no_assignee') }}</td>
                            <td class="text-bold text-red">{{ $ticket->due_date?->format('d/m/Y') }}</td>
                        </tr>
                    @endforeach
                    @foreach($atRisk['due_soon'] as $ticket)
                        <tr>
                            <td><span class="badge badge-amber">{{ __('daily_report.due_soon') }}</span></td>
                            <td class="font-mono">{{ $ticket->public_id }}</td>
                            <td class="text-bold">{{ Str::limit($ticket->subject, 50) }}</td>
                            <td>{{ $ticket->group?->name ?? '—' }}</td>
                            <td>{{ $ticket->priority?->name ?? '—' }}</td>
                            <td>{{ $ticket->assignees->pluck('name')->join(', ') ?: __('daily_report.no_assignee') }}</td>
                            <td class="text-bold text-amber">{{ $ticket->due_date?->format('d/m/Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- ─── TICKETS ACTIFS ─────────────────────── --}}
        @if($activeTickets->isNotEmpty())
            <div class="section-title">{{ __('daily_report.active_tickets') }} <span>({{ $activeTickets->count() }})</span></div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:9%">{{ __('daily_report.reference') }}</th>
                        <th style="width:25%">{{ __('daily_report.subject') }}</th>
                        <th style="width:11%">{{ __('daily_report.group') }}</th>
                        <th style="width:9%">{{ __('daily_report.priority') }}</th>
                        <th style="width:16%">{{ __('daily_report.assignees') }}</th>
                        <th style="width:11%">{{ __('daily_report.updated_at') }}</th>
                        <th style="width:9%">{{ __('daily_report.due_date') }}</th>
                        <th style="width:10%">{{ __('daily_report.status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activeTickets as $ticket)
                        @php
                            $statusBadge = match($ticket->status) {
                                \App\Enums\TicketStatus::Open => 'badge-blue',
                                \App\Enums\TicketStatus::InProgress => 'badge-amber',
                                \App\Enums\TicketStatus::Pending => 'badge-slate',
                                default => 'badge-slate',
                            };
                            $statusLabel = match($ticket->status) {
                                \App\Enums\TicketStatus::Open => __('daily_report.open'),
                                \App\Enums\TicketStatus::InProgress => __('daily_report.in_progress'),
                                \App\Enums\TicketStatus::Pending => __('daily_report.pending'),
                                default => $ticket->status->value,
                            };
                        @endphp
                        <tr>
                            <td class="font-mono">{{ $ticket->public_id }}</td>
                            <td class="text-bold">{{ Str::limit($ticket->subject, 50) }}</td>
                            <td>{{ $ticket->group?->name ?? '—' }}</td>
                            <td>{{ $ticket->priority?->name ?? '—' }}</td>
                            <td>{{ $ticket->assignees->pluck('name')->join(', ') ?: __('daily_report.no_assignee') }}</td>
                            <td>{{ $ticket->updated_at?->format('d/m/Y H:i') }}</td>
                            <td>{{ $ticket->due_date?->format('d/m/Y') ?? '—' }}</td>
                            <td><span class="badge {{ $statusBadge }}">{{ $statusLabel }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- ─── PERFORMANCE AGENTS ─────────────────── --}}
        @if(!empty($agentPerf))
            <div class="section-title">{{ __('daily_report.agent_performance') }}</div>
            <table class="data-table agent-table">
                <thead>
                    <tr>
                        <th style="width:50%">{{ __('daily_report.agent') }}</th>
                        <th style="width:25%" class="text-center">{{ __('daily_report.handled') }}</th>
                        <th style="width:25%" class="text-center">{{ __('daily_report.resolved') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($agentPerf as $agent)
                        <tr>
                            <td class="text-bold">{{ $agent['name'] }}</td>
                            <td class="text-center">{{ $agent['handled'] }}</td>
                            <td class="text-center">{{ $agent['resolved'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="footer">
        {{ $orgName }} &middot; {{ __('daily_report.title') }} &middot; {{ \Illuminate\Support\Carbon::parse($date)->format('d/m/Y') }} &middot; {{ __('task_report.report_generated', ['date' => now()->format('d/m/Y H:i')]) }}
    </div>
</body>
</html>
