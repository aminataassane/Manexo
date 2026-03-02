<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Models\Organization;
use App\Models\Ticket;
use App\Models\TicketChecklistItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SharedTaskReportController extends Controller
{
    public function __invoke(Request $request)
    {
        $orgId = (int) $request->query('org');
        $from = Carbon::parse($request->query('from'))->startOfDay();
        $to = Carbon::parse($request->query('to'))->endOfDay();

        $org = Organization::findOrFail($orgId);

        $base = TicketChecklistItem::query()
            ->join('tickets', 'ticket_checklist_items.ticket_id', '=', 'tickets.id')
            ->where('tickets.organization_id', $orgId)
            ->where('ticket_checklist_items.is_done', true)
            ->whereBetween('ticket_checklist_items.done_at', [$from, $to])
            ->select('ticket_checklist_items.*');

        $tasksTotal = (clone $base)->count();

        $closedQuery = Ticket::query()
            ->where('organization_id', $orgId)
            ->whereIn('status', [TicketStatus::Resolved, TicketStatus::Closed])
            ->whereBetween('updated_at', [$from, $to]);

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

        $topCategoryClosed = $byCategoryClosed[0] ?? null;
        $topUserClosed = $byUserClosed[0] ?? null;

        $stats = compact('closedTicketsCount', 'tasksTotal', 'byCategoryClosed', 'byUserClosed', 'topCategoryClosed', 'topUserClosed');

        $closedTickets = Ticket::query()
            ->where('organization_id', $orgId)
            ->whereIn('status', [TicketStatus::Resolved, TicketStatus::Closed])
            ->whereBetween('updated_at', [$from, $to])
            ->with(['category:id,name', 'creator:id,name', 'assignees:id,name', 'closedByUser:id,name'])
            ->withCount('checklistItems')
            ->withCount(['checklistItems as checklist_done_count' => fn ($q) => $q->where('is_done', true)])
            ->orderByDesc('updated_at')
            ->limit(200)
            ->get();

        return view('reports.shared-task-report', [
            'stats' => $stats,
            'closedTickets' => $closedTickets,
            'from' => $from,
            'to' => $to,
            'orgName' => $org->name,
        ]);
    }
}
