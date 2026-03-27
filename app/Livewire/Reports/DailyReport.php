<?php

namespace App\Livewire\Reports;

use App\Enums\Permission;
use App\Enums\TicketStatus;
use App\Helpers\CacheHelper;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketGroup;
use App\Models\TicketPriority;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('layouts.manexo-app')]
#[Title('Rapport journalier')]
class DailyReport extends Component
{
    /** 1 = shell instantané, 2 = KPI + listes (progressif). */
    public int $loadStage = 1;

    public function loadReportBody(): void
    {
        if ($this->loadStage < 2) {
            $this->loadStage = 2;
        }
    }

    public string $date = '';

    public string $filterGroup = '';

    public string $filterAgent = '';

    public string $filterCategory = '';

    public string $filterPriority = '';

    public string $activeTab = 'in_progress';

    public function mount(): void
    {
        $this->date = now()->toDateString();
    }

    private function orgId(): int
    {
        return (int) session('current_organization_id');
    }

    /** @return array{0: Carbon, 1: Carbon} */
    private function dateRange(): array
    {
        $day = Carbon::parse($this->date ?: now()->toDateString());

        return [$day->copy()->startOfDay(), $day->copy()->endOfDay()];
    }

    /** Base ticket query scoped to org + active filters. */
    private function baseTicketQuery()
    {
        $query = Ticket::query()
            ->where('tickets.organization_id', $this->orgId());

        if ($this->filterGroup !== '') {
            $query->where('tickets.ticket_group_id', (int) $this->filterGroup);
        }
        if ($this->filterAgent !== '') {
            $query->whereHas('assignees', fn ($q) => $q->where('users.id', (int) $this->filterAgent));
        }
        if ($this->filterCategory !== '') {
            $query->where('tickets.ticket_category_id', (int) $this->filterCategory);
        }
        if ($this->filterPriority !== '') {
            $query->where('tickets.ticket_priority_id', (int) $this->filterPriority);
        }

        return $query;
    }

    private function hasActiveFilters(): bool
    {
        return $this->filterGroup !== ''
            || $this->filterAgent !== ''
            || $this->filterCategory !== ''
            || $this->filterPriority !== '';
    }

    /**
     * Compute summary KPIs for the selected date.
     *
     * @return array<string, mixed>
     */
    private function computeSummary(Carbon $start, Carbon $end): array
    {
        $base = $this->baseTicketQuery();

        // Tickets created on this day
        $created = (clone $base)
            ->whereBetween('tickets.created_at', [$start, $end])
            ->selectRaw('count(*) as total')
            ->selectRaw("count(*) filter (where status = 'open') as open_count")
            ->selectRaw("count(*) filter (where status = 'in_progress') as in_progress_count")
            ->selectRaw("count(*) filter (where status = 'pending') as pending_count")
            ->selectRaw("count(*) filter (where status = 'resolved') as resolved_count")
            ->selectRaw("count(*) filter (where status = 'closed') as closed_count")
            ->first();

        // Tickets resolved/closed on this day (by closed_at or updated_at with resolved/closed status)
        $resolvedToday = (clone $base)
            ->whereIn('tickets.status', [TicketStatus::Resolved, TicketStatus::Closed])
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('tickets.closed_at', [$start, $end])
                    ->orWhere(function ($q2) use ($start, $end) {
                        $q2->whereNull('tickets.closed_at')
                            ->whereBetween('tickets.updated_at', [$start, $end]);
                    });
            })
            ->count();

        // Current backlog (open/in_progress/pending tickets created <= end of day)
        $backlog = (clone $base)
            ->where('tickets.created_at', '<=', $end)
            ->whereIn('tickets.status', [TicketStatus::Open, TicketStatus::InProgress, TicketStatus::Pending])
            ->selectRaw("count(*) filter (where status = 'open') as open_count")
            ->selectRaw("count(*) filter (where status = 'in_progress') as in_progress_count")
            ->selectRaw("count(*) filter (where status = 'pending') as pending_count")
            ->first();

        // Average first response time — tickets whose first agent response fell on this day
        $avgFirstResponse = (clone $base)
            ->whereExists(function ($sub) use ($start, $end) {
                $sub->select(DB::raw(1))
                    ->from('ticket_messages as tm')
                    ->whereColumn('tm.ticket_id', 'tickets.id')
                    ->whereColumn('tm.user_id', '!=', 'tickets.created_by')
                    ->where('tm.type', 'message')
                    ->whereRaw('tm.created_at = (select min(tm2.created_at) from ticket_messages tm2 where tm2.ticket_id = tickets.id and tm2.user_id != tickets.created_by and tm2.type = \'message\')')
                    ->whereBetween('tm.created_at', [$start, $end]);
            })
            ->selectRaw("avg(extract(epoch from (
                (select min(tm.created_at) from ticket_messages tm
                 where tm.ticket_id = tickets.id
                 and tm.user_id != tickets.created_by
                 and tm.type = 'message')
                - tickets.created_at
            ))) as avg_seconds")
            ->value('avg_seconds');

        // Average resolution time — tickets resolved/closed on this day
        $avgResolution = (clone $base)
            ->whereIn('tickets.status', [TicketStatus::Resolved, TicketStatus::Closed])
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('tickets.closed_at', [$start, $end])
                    ->orWhere(function ($q2) use ($start, $end) {
                        $q2->whereNull('tickets.closed_at')
                            ->whereBetween('tickets.updated_at', [$start, $end]);
                    });
            })
            ->selectRaw('avg(extract(epoch from (coalesce(tickets.closed_at, tickets.updated_at) - tickets.created_at))) as avg_seconds')
            ->value('avg_seconds');

        return [
            'created_total' => (int) ($created?->total ?? 0),
            'created_open' => (int) ($created?->open_count ?? 0),
            'created_in_progress' => (int) ($created?->in_progress_count ?? 0),
            'created_pending' => (int) ($created?->pending_count ?? 0),
            'created_resolved' => (int) ($created?->resolved_count ?? 0),
            'created_closed' => (int) ($created?->closed_count ?? 0),
            'resolved_today' => $resolvedToday,
            'backlog_open' => (int) ($backlog?->open_count ?? 0),
            'backlog_in_progress' => (int) ($backlog?->in_progress_count ?? 0),
            'backlog_pending' => (int) ($backlog?->pending_count ?? 0),
            'avg_first_response_seconds' => $avgFirstResponse ? (float) $avgFirstResponse : null,
            'avg_resolution_seconds' => $avgResolution ? (float) $avgResolution : null,
        ];
    }

    #[Computed]
    public function activeTickets()
    {
        $query = $this->baseTicketQuery();

        $statuses = match ($this->activeTab) {
            'in_progress' => [TicketStatus::InProgress],
            'pending' => [TicketStatus::Pending],
            default => [TicketStatus::Open, TicketStatus::InProgress, TicketStatus::Pending],
        };

        return $query
            ->whereIn('tickets.status', $statuses)
            ->with(['category:id,name', 'priority:id,name', 'group:id,name', 'assignees:id,name'])
            ->orderByDesc('tickets.updated_at')
            ->limit(50)
            ->get();
    }

    #[Computed]
    public function atRiskTickets(): array
    {
        $now = Carbon::now();
        $base = $this->baseTicketQuery()
            ->whereNotNull('tickets.due_date')
            ->whereIn('tickets.status', [TicketStatus::Open, TicketStatus::InProgress, TicketStatus::Pending])
            ->with(['category:id,name', 'priority:id,name', 'group:id,name', 'assignees:id,name']);

        $overdue = (clone $base)
            ->where('tickets.due_date', '<', $now->toDateString())
            ->orderBy('tickets.due_date')
            ->limit(20)
            ->get();

        $dueSoon = (clone $base)
            ->where('tickets.due_date', '>=', $now->toDateString())
            ->where('tickets.due_date', '<=', $now->copy()->addDay()->toDateString())
            ->orderBy('tickets.due_date')
            ->limit(20)
            ->get();

        return ['overdue' => $overdue, 'due_soon' => $dueSoon];
    }

    #[Computed]
    public function agentPerformance(): array
    {
        [$start, $end] = $this->dateRange();

        return DB::table('ticket_messages as tm')
            ->join('tickets as t', 't.id', '=', 'tm.ticket_id')
            ->join('users as u', 'u.id', '=', 'tm.user_id')
            ->where('t.organization_id', $this->orgId())
            ->whereBetween('tm.created_at', [$start, $end])
            ->where('tm.type', 'message')
            ->whereColumn('tm.user_id', '!=', 't.created_by')
            ->select(
                'u.id as user_id',
                'u.name as name',
                DB::raw('count(distinct t.id) as handled'),
                DB::raw("count(distinct t.id) filter (where t.status in ('resolved','closed')) as resolved"),
            )
            ->groupBy('u.id', 'u.name')
            ->orderByDesc('handled')
            ->limit(10)
            ->get()
            ->map(fn ($r) => [
                'user_id' => $r->user_id,
                'name' => $r->name,
                'handled' => (int) $r->handled,
                'resolved' => (int) $r->resolved,
            ])
            ->all();
    }

    #[Computed]
    public function distributionByCategory(): array
    {
        [$start, $end] = $this->dateRange();

        return $this->baseTicketQuery()
            ->whereBetween('tickets.created_at', [$start, $end])
            ->leftJoin('ticket_categories as tc', 'tickets.ticket_category_id', '=', 'tc.id')
            ->selectRaw("coalesce(tc.name, ?) as name, count(*) as c", [__('daily_report.no_category')])
            ->groupBy('tc.name')
            ->orderByDesc('c')
            ->limit(8)
            ->get()
            ->map(fn ($r) => ['name' => (string) $r->name, 'count' => (int) $r->c])
            ->all();
    }

    #[Computed]
    public function distributionByPriority(): array
    {
        [$start, $end] = $this->dateRange();

        $priorityColors = ['#ef4444', '#f59e0b', '#3b82f6', '#6b7280', '#10b981', '#8b5cf6', '#ec4899', '#94a3b8'];

        return $this->baseTicketQuery()
            ->whereBetween('tickets.created_at', [$start, $end])
            ->leftJoin('ticket_priorities as tp', 'tickets.ticket_priority_id', '=', 'tp.id')
            ->selectRaw("coalesce(tp.name, ?) as name, count(*) as c", [__('daily_report.no_priority')])
            ->groupBy('tp.name')
            ->orderByDesc('c')
            ->limit(8)
            ->get()
            ->values()
            ->map(fn ($r, $i) => ['name' => (string) $r->name, 'color' => $priorityColors[$i % count($priorityColors)], 'count' => (int) $r->c])
            ->all();
    }

    private function getLogoBase64(?object $org): ?string
    {
        if ($org && $org->logo_path) {
            $path = storage_path('app/public/' . ltrim($org->logo_path, '/'));
            if (file_exists($path)) {
                $mime = mime_content_type($path) ?: 'image/png';

                return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
            }
        }

        return null;
    }

    public function exportCsv(): StreamedResponse
    {
        [$start, $end] = $this->dateRange();
        $summary = $this->computeSummary($start, $end);
        $org = request()->attributes->get('currentOrganization');
        $orgName = $org?->name ?? '';

        return response()->streamDownload(function () use ($summary, $orgName) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF"); // UTF-8 BOM

            // Manexo branding header
            fputcsv($handle, ['Manexo — ' . $orgName], ';');
            fputcsv($handle, [__('daily_report.title') . ' — ' . $this->date], ';');
            fputcsv($handle, [__('task_report.report_generated', ['date' => now()->format('d/m/Y H:i')])], ';');
            fputcsv($handle, [], ';');

            // Section 1: Summary
            fputcsv($handle, [__('daily_report.export_section_summary')], ';');
            fputcsv($handle, [__('daily_report.export_date'), $this->date], ';');
            fputcsv($handle, [__('daily_report.created_total'), $summary['created_total']], ';');
            fputcsv($handle, [__('daily_report.resolved_closed_today'), $summary['resolved_today']], ';');
            fputcsv($handle, [__('daily_report.avg_first_response'), $this->formatDuration($summary['avg_first_response_seconds'])], ';');
            fputcsv($handle, [__('daily_report.avg_resolution_time'), $this->formatDuration($summary['avg_resolution_seconds'])], ';');
            fputcsv($handle, [__('daily_report.backlog') . ' - ' . __('daily_report.open'), $summary['backlog_open']], ';');
            fputcsv($handle, [__('daily_report.backlog') . ' - ' . __('daily_report.in_progress'), $summary['backlog_in_progress']], ';');
            fputcsv($handle, [__('daily_report.backlog') . ' - ' . __('daily_report.pending'), $summary['backlog_pending']], ';');
            fputcsv($handle, [], ';');

            // Section 2: At-risk tickets
            fputcsv($handle, [__('daily_report.export_section_at_risk')], ';');
            fputcsv($handle, [
                __('daily_report.export_risk_type'),
                __('daily_report.reference'),
                __('daily_report.subject'),
                __('daily_report.group'),
                __('daily_report.priority'),
                __('daily_report.assignees'),
                __('daily_report.due_date'),
                __('daily_report.status'),
            ], ';');

            $atRisk = $this->atRiskTickets;
            foreach (['overdue', 'due_soon'] as $type) {
                foreach ($atRisk[$type] as $ticket) {
                    fputcsv($handle, [
                        $type === 'overdue' ? __('daily_report.overdue') : __('daily_report.due_soon'),
                        $ticket->public_id,
                        $ticket->subject,
                        $ticket->group?->name ?? __('daily_report.no_group_label'),
                        $ticket->priority?->name ?? __('daily_report.no_priority'),
                        $ticket->assignees->pluck('name')->join(', ') ?: __('daily_report.no_assignee'),
                        $ticket->due_date?->format('d/m/Y') ?? '',
                        $ticket->status->value,
                    ], ';');
                }
            }
            fputcsv($handle, [], ';');

            // Section 3: Active tickets
            fputcsv($handle, [__('daily_report.export_section_active')], ';');
            fputcsv($handle, [
                __('daily_report.reference'),
                __('daily_report.subject'),
                __('daily_report.group'),
                __('daily_report.priority'),
                __('daily_report.assignees'),
                __('daily_report.updated_at'),
                __('daily_report.due_date'),
                __('daily_report.status'),
            ], ';');

            $this->baseTicketQuery()
                ->whereIn('tickets.status', [TicketStatus::Open, TicketStatus::InProgress, TicketStatus::Pending])
                ->with(['group:id,name', 'priority:id,name', 'assignees:id,name'])
                ->orderByDesc('tickets.updated_at')
                ->chunk(100, function ($tickets) use ($handle) {
                    foreach ($tickets as $ticket) {
                        fputcsv($handle, [
                            $ticket->public_id,
                            $ticket->subject,
                            $ticket->group?->name ?? __('daily_report.no_group_label'),
                            $ticket->priority?->name ?? __('daily_report.no_priority'),
                            $ticket->assignees->pluck('name')->join(', ') ?: __('daily_report.no_assignee'),
                            $ticket->updated_at?->format('d/m/Y H:i') ?? '',
                            $ticket->due_date?->format('d/m/Y') ?? '',
                            $ticket->status->value,
                        ], ';');
                    }
                });

            fclose($handle);
        }, 'rapport-journalier-' . $this->date . '.csv', [
            'Content-Type' => 'text/csv; charset=utf-8',
        ]);
    }

    public function exportPdf(): StreamedResponse
    {
        [$start, $end] = $this->dateRange();
        $summary = $this->computeSummary($start, $end);
        $atRisk = $this->atRiskTickets;
        $agentPerf = $this->agentPerformance;

        $activeTickets = $this->baseTicketQuery()
            ->whereIn('tickets.status', [TicketStatus::Open, TicketStatus::InProgress, TicketStatus::Pending])
            ->with(['category:id,name', 'priority:id,name', 'group:id,name', 'assignees:id,name'])
            ->orderByDesc('tickets.updated_at')
            ->limit(100)
            ->get();

        $org = request()->attributes->get('currentOrganization');
        $orgName = $org?->name ?? '';
        $logoBase64 = $this->getLogoBase64($org);

        $pdf = Pdf::loadView('pdf.daily-report', [
            'summary' => $summary,
            'atRisk' => $atRisk,
            'activeTickets' => $activeTickets,
            'agentPerf' => $agentPerf,
            'date' => $this->date,
            'orgName' => $orgName,
            'logoBase64' => $logoBase64,
            'formatDuration' => fn (?float $s) => $this->formatDuration($s),
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn () => print $pdf->output(),
            'rapport-journalier-' . $this->date . '.pdf',
            ['Content-Type' => 'application/pdf'],
        );
    }

    private function formatDuration(?float $seconds): string
    {
        if ($seconds === null || $seconds <= 0) {
            return __('daily_report.na');
        }
        $hours = (int) floor($seconds / 3600);
        $minutes = (int) round(($seconds % 3600) / 60);

        if ($hours > 0) {
            return $hours . __('daily_report.hours_short') . ' ' . $minutes . __('daily_report.minutes_short');
        }

        return $minutes . __('daily_report.minutes_short');
    }

    public function render()
    {
        $user = Auth::user();
        $orgId = $this->orgId();

        if (! $user || ! $orgId) {
            return redirect()->route('organizations.select');
        }

        if (! ($user instanceof User) || ! $user->hasPermission(Permission::ReportsView)) {
            abort(403);
        }

        [$start, $end] = $this->dateRange();

        if ($this->loadStage < 2) {
            $summary = [
                'created_total' => 0,
                'created_open' => 0,
                'created_in_progress' => 0,
                'created_pending' => 0,
                'created_resolved' => 0,
                'created_closed' => 0,
                'resolved_today' => 0,
                'backlog_open' => 0,
                'backlog_in_progress' => 0,
                'backlog_pending' => 0,
                'avg_first_response_seconds' => null,
                'avg_resolution_seconds' => null,
            ];
        } else {
            // Cache summary only if date = today AND no filters active
            $isToday = $this->date === now()->toDateString();
            if ($isToday && ! $this->hasActiveFilters()) {
                $summary = Cache::remember(
                    CacheHelper::dailyReportKey($orgId, $this->date),
                    CacheHelper::TTL,
                    fn () => $this->computeSummary($start, $end),
                );
            } else {
                $summary = $this->computeSummary($start, $end);
            }
        }

        // Load filter options from cache
        $categories = Cache::remember(CacheHelper::categoriesKey($orgId, true), CacheHelper::TTL, function () use ($orgId) {
            return TicketCategory::where('organization_id', $orgId)->where('is_active', true)->orderBy('name')->get(['id', 'name']);
        });

        $priorities = Cache::remember(CacheHelper::prioritiesKey($orgId, true), CacheHelper::TTL, function () use ($orgId) {
            return TicketPriority::where('organization_id', $orgId)->where('is_active', true)->orderBy('level')->get(['id', 'name']);
        });

        $groups = Cache::remember(CacheHelper::ticketGroupsKey($orgId, true), CacheHelper::TTL, function () use ($orgId) {
            return TicketGroup::where('organization_id', $orgId)->where('is_active', true)->orderBy('name')->get(['id', 'name']);
        });

        $agents = Cache::remember(CacheHelper::membersKey($orgId), CacheHelper::TTL, function () use ($orgId) {
            return User::whereHas('organizations', fn ($q) => $q->where('organization_id', $orgId))
                ->orderBy('name')
                ->get(['id', 'name']);
        });

        return view('livewire.reports.daily-report', compact(
            'summary', 'categories', 'priorities', 'groups', 'agents',
        ));
    }
}
