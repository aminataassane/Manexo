<?php

namespace App\Http\Controllers;

use App\Enums\Permission;
use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\TicketChecklistItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TaskReportExportController extends Controller
{
    private function orgId(Request $request): int
    {
        return (int) session('current_organization_id');
    }

    private function canViewAll(Request $request): bool
    {
        $user = $request->user();

        return $user && $user->hasPermission(Permission::ReportsView);
    }

    /** @return array{0: Carbon, 1: Carbon} */
    private function dateRange(Request $request): array
    {
        $period = $request->query('period', 'week');
        $dateFrom = $request->query('dateFrom', '');
        $dateTo = $request->query('dateTo', '');

        if ($period === 'custom' && $dateFrom && $dateTo) {
            return [
                Carbon::parse($dateFrom)->startOfDay(),
                Carbon::parse($dateTo)->endOfDay(),
            ];
        }

        $now = Carbon::now();

        return match ($period) {
            'today' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'month' => [$now->copy()->startOfMonth(), $now->copy()->endOfDay()],
            default => [$now->copy()->startOfWeek(), $now->copy()->endOfDay()],
        };
    }

    private function baseQuery(Request $request, Carbon $from, Carbon $to)
    {
        $orgId = $this->orgId($request);
        $query = TicketChecklistItem::query()
            ->join('tickets', 'ticket_checklist_items.ticket_id', '=', 'tickets.id')
            ->where('tickets.organization_id', $orgId)
            ->where('ticket_checklist_items.is_done', true)
            ->whereBetween('ticket_checklist_items.done_at', [$from, $to])
            ->select('ticket_checklist_items.*');

        if (! $this->canViewAll($request)) {
            $userId = $request->user()->id;
            $query->where(function ($q) use ($userId) {
                $q->where('ticket_checklist_items.assigned_to', $userId)
                    ->orWhere('ticket_checklist_items.done_by', $userId);
            });
        }

        return $query;
    }

    private function closedTicketsQuery(Request $request, Carbon $from, Carbon $to)
    {
        $orgId = $this->orgId($request);
        $query = Ticket::query()
            ->where('tickets.organization_id', $orgId)
            ->whereIn('tickets.status', [TicketStatus::Resolved, TicketStatus::Closed])
            ->whereBetween('tickets.updated_at', [$from, $to]);

        if (! $this->canViewAll($request)) {
            $userId = $request->user()->id;
            $query->where(function ($q) use ($userId) {
                $q->where('tickets.created_by', $userId)
                    ->orWhereHas('assignees', fn ($a) => $a->where('users.id', $userId));
            });
        }

        return $query;
    }

    public function __invoke(Request $request, string $format): StreamedResponse
    {
        $user = $request->user();
        if (! $user || ! $user->hasAnyPermission([Permission::ReportsView, Permission::ReportsViewTasks])) {
            abort(403);
        }

        return $format === 'pdf'
            ? $this->exportPdf($request)
            : $this->exportCsv($request);
    }

    private function exportCsv(Request $request): StreamedResponse
    {
        [$from, $to] = $this->dateRange($request);

        return response()->streamDownload(function () use ($request, $from, $to) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                __('task_report.export_type'),
                __('task_report.ticket'),
                __('task_report.category'),
                __('task_report.closed_by'),
                __('task_report.closed_at'),
                __('task_report.checklist_progress'),
            ], ';');

            $this->closedTicketsQuery($request, $from, $to)
                ->with(['category:id,name', 'creator:id,name', 'assignees:id,name'])
                ->withCount('checklistItems')
                ->withCount(['checklistItems as checklist_done_count' => fn ($q) => $q->where('is_done', true)])
                ->orderByDesc('updated_at')
                ->chunk(100, function ($tickets) use ($handle) {
                    foreach ($tickets as $ticket) {
                        $closedBy = $ticket->assignees->first()?->name ?? $ticket->creator?->name ?? '';
                        $done = (int) ($ticket->checklist_done_count ?? 0);
                        $total = (int) ($ticket->checklist_items_count ?? 0);
                        $subTasks = $total > 0 ? "{$done}/{$total}" : '';
                        fputcsv($handle, [
                            __('task_report.export_type_closed_ticket'),
                            $ticket->subject,
                            $ticket->category?->name ?? '',
                            $closedBy,
                            $ticket->updated_at->format('d/m/Y H:i'),
                            $subTasks,
                        ], ';');
                    }
                });

            fclose($handle);
        }, 'taches-terminees-' . now()->format('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv; charset=utf-8',
        ]);
    }

    private function exportPdf(Request $request): StreamedResponse
    {
        [$from, $to] = $this->dateRange($request);

        $closedQuery = $this->closedTicketsQuery($request, $from, $to);
        $closedTicketsCount = (clone $closedQuery)->count();
        $byCategoryClosed = (clone $closedQuery)
            ->selectRaw('ticket_categories.name as category_name, count(tickets.id) as count')
            ->leftJoin('ticket_categories', 'tickets.ticket_category_id', '=', 'ticket_categories.id')
            ->groupBy('ticket_categories.name')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->map(fn ($r) => ['name' => $r->category_name ?? __('task_report.no_category'), 'count' => (int) $r->count])
            ->all();
        $byUserClosed = (clone $closedQuery)
            ->selectRaw('COALESCE(closed_by_user.name, creator_user.name) as user_name, count(tickets.id) as count')
            ->leftJoin('users as closed_by_user', 'tickets.closed_by', '=', 'closed_by_user.id')
            ->leftJoin('users as creator_user', 'tickets.created_by', '=', 'creator_user.id')
            ->groupByRaw('COALESCE(closed_by_user.name, creator_user.name)')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->map(fn ($r) => ['name' => $r->user_name ?? __('task_report.not_assigned'), 'count' => (int) $r->count])
            ->all();
        $stats = [
            'closedTicketsCount' => $closedTicketsCount,
            'byCategoryClosed' => $byCategoryClosed,
            'byUserClosed' => $byUserClosed,
            'topCategoryClosed' => $byCategoryClosed[0] ?? null,
            'topUserClosed' => $byUserClosed[0] ?? null,
            'tasksTotal' => (clone $this->baseQuery($request, $from, $to))->count(),
            'byUserTasks' => [],
            'byCategoryTasks' => [],
            'topUserTasks' => null,
            'topCategoryTasks' => null,
        ];

        $closedTickets = $this->closedTicketsQuery($request, $from, $to)
            ->with(['category:id,name', 'creator:id,name', 'assignees:id,name', 'closedByUser:id,name'])
            ->withCount('checklistItems')
            ->withCount(['checklistItems as checklist_done_count' => fn ($q) => $q->where('is_done', true)])
            ->orderByDesc('updated_at')
            ->limit(500)
            ->get();

        $org = $request->attributes->get('currentOrganization');
        $orgName = $org?->name ?? '';

        $pdf = Pdf::loadView('pdf.task-report', [
            'stats' => $stats,
            'closedTickets' => $closedTickets,
            'from' => $from,
            'to' => $to,
            'orgName' => $orgName,
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn () => print $pdf->output(),
            'rapport-taches-' . now()->format('Y-m-d') . '.pdf',
            ['Content-Type' => 'application/pdf'],
        );
    }
}
