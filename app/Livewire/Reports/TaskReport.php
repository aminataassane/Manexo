<?php

namespace App\Livewire\Reports;

use App\Enums\Permission;
use App\Enums\TicketStatus;
use App\Models\OrganizationMembership;
use App\Models\Ticket;
use App\Models\TicketChecklistItem;
use App\Notifications\TaskReportSharedNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('layouts.manexo-app')]
#[Title('Rapport des tâches')]
class TaskReport extends Component
{
    use WithPagination;

    public string $period = 'week';

    public string $dateFrom = '';

    public string $dateTo = '';

    public bool $showShareModal = false;

    public ?int $shareUserId = null;

    public string $lastSignedUrl = '';

    private function orgId(): int
    {
        return (int) session('current_organization_id');
    }

    /** Full report (all org tasks) */
    private function canViewAll(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return $user && $user->hasPermission(Permission::ReportsView);
    }

    /** Can share the report */
    private function canShare(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return $user && $user->hasPermission(Permission::ReportsShareTasks);
    }

    public function setPeriod(string $value): void
    {
        $this->period = $value;
        $this->resetPage();
        $this->resetPage('closed_page');
        $this->lastSignedUrl = '';
    }

    public function updatedPeriod(): void
    {
        $this->resetPage();
        $this->resetPage('closed_page');
        $this->lastSignedUrl = '';
    }

    public function updatedDateFrom(): void
    {
        $this->resetPage();
        $this->resetPage('closed_page');
    }

    public function updatedDateTo(): void
    {
        $this->resetPage();
        $this->resetPage('closed_page');
    }

    /** @return array{Carbon, Carbon} */
    private function dateRange(): array
    {
        if ($this->period === 'custom' && $this->dateFrom && $this->dateTo) {
            return [
                Carbon::parse($this->dateFrom)->startOfDay(),
                Carbon::parse($this->dateTo)->endOfDay(),
            ];
        }

        $now = Carbon::now();

        return match ($this->period) {
            'today' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'month' => [$now->copy()->startOfMonth(), $now->copy()->endOfDay()],
            default => [$now->copy()->startOfWeek(), $now->copy()->endOfDay()], // week
        };
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder<TicketChecklistItem>
     */
    private function baseQuery(Carbon $from, Carbon $to)
    {
        $query = TicketChecklistItem::query()
            ->join('tickets', 'ticket_checklist_items.ticket_id', '=', 'tickets.id')
            ->where('tickets.organization_id', $this->orgId())
            ->where('ticket_checklist_items.is_done', true)
            ->whereBetween('ticket_checklist_items.done_at', [$from, $to])
            ->select('ticket_checklist_items.*');

        // Without ReportsView: only their own tasks (assigned to them or completed by them)
        if (! $this->canViewAll()) {
            $userId = Auth::id();
            $query->where(function ($q) use ($userId) {
                $q->where('ticket_checklist_items.assigned_to', $userId)
                  ->orWhere('ticket_checklist_items.done_by', $userId);
            });
        }

        return $query;
    }

    /**
     * Tickets résolus ou fermés dans la période (sans checklist obligatoire).
     *
     * @return \Illuminate\Database\Eloquent\Builder<Ticket>
     */
    private function closedTicketsQuery(Carbon $from, Carbon $to)
    {
        $query = Ticket::query()
            ->where('tickets.organization_id', $this->orgId())
            ->whereIn('tickets.status', [TicketStatus::Resolved, TicketStatus::Closed])
            ->whereBetween('tickets.updated_at', [$from, $to]);

        if (! $this->canViewAll()) {
            $userId = Auth::id();
            $query->where(function ($q) use ($userId) {
                $q->where('tickets.created_by', $userId)
                    ->orWhereHas('assignees', fn ($a) => $a->where('users.id', $userId));
            });
        }

        return $query;
    }

    /**
     * Stats: primary = tickets clôturés, secondary = sous-tâches (checklist).
     */
    private function computeStats(Carbon $from, Carbon $to): array
    {
        $closedQuery = $this->closedTicketsQuery($from, $to);
        $closedTicketsCount = (clone $closedQuery)->count();

        // Primary: répartition des tickets clôturés par catégorie
        $byCategoryClosed = (clone $closedQuery)
            ->selectRaw('ticket_categories.name as category_name, count(tickets.id) as count')
            ->leftJoin('ticket_categories', 'tickets.ticket_category_id', '=', 'ticket_categories.id')
            ->groupBy('ticket_categories.name')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->map(fn ($r) => ['name' => $r->category_name ?? __('task_report.no_category'), 'count' => (int) $r->count])
            ->all();

        // Primary: répartition par qui a clôturé (closed_by), avec repli sur créateur (created_by) si closed_by vide (anciens tickets)
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

        // Secondary: sous-tâches (checklist items)
        $base = $this->baseQuery($from, $to);
        $tasksTotal = (clone $base)->count();

        $byUserTasks = (clone $base)
            ->select([])
            ->leftJoin('users as done_user', 'ticket_checklist_items.done_by', '=', 'done_user.id')
            ->selectRaw('done_user.name as user_name, count(*) as count')
            ->groupBy('done_user.name')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->map(fn ($r) => ['name' => $r->user_name ?? __('task_report.not_assigned'), 'count' => (int) $r->count])
            ->all();

        $byCategoryTasks = (clone $base)
            ->select([])
            ->leftJoin('ticket_categories as tc', 'tickets.ticket_category_id', '=', 'tc.id')
            ->selectRaw('tc.name as category_name, count(*) as count')
            ->groupBy('tc.name')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->map(fn ($r) => ['name' => $r->category_name ?? __('task_report.no_category'), 'count' => (int) $r->count])
            ->all();

        $topUserTasks = $byUserTasks[0] ?? null;
        $topCategoryTasks = $byCategoryTasks[0] ?? null;

        return compact(
            'closedTicketsCount',
            'byCategoryClosed',
            'byUserClosed',
            'topCategoryClosed',
            'topUserClosed',
            'tasksTotal',
            'byUserTasks',
            'byCategoryTasks',
            'topUserTasks',
            'topCategoryTasks',
        );
    }

    #[Computed]
    public function closedTickets()
    {
        [$from, $to] = $this->dateRange();

        return $this->closedTicketsQuery($from, $to)
            ->with(['category:id,name', 'creator:id,name', 'assignees:id,name', 'closedByUser:id,name'])
            ->withCount('checklistItems')
            ->withCount(['checklistItems as checklist_done_count' => fn ($q) => $q->where('is_done', true)])
            ->orderByDesc('updated_at')
            ->paginate(20, ['*'], 'closed_page');
    }

    #[Computed]
    public function completedItems()
    {
        [$from, $to] = $this->dateRange();

        return $this->baseQuery($from, $to)
            ->with([
                'ticket:id,subject,ticket_category_id',
                'ticket.category:id,name',
                'assignee:id,name',
                'doneByUser:id,name',
            ])
            ->orderByDesc('ticket_checklist_items.done_at')
            ->paginate(20);
    }

    public function exportCsv(): StreamedResponse
    {
        [$from, $to] = $this->dateRange();

        return response()->streamDownload(function () use ($from, $to) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF"); // UTF-8 BOM

            fputcsv($handle, [
                __('task_report.export_type'),
                __('task_report.task'),
                __('task_report.ticket'),
                __('task_report.category'),
                __('task_report.assigned_to'),
                __('task_report.completed_by'),
                __('task_report.completed_at'),
                __('task_report.due_date'),
            ], ';');

            // Section principale : tickets clôturés d'abord
            $this->closedTicketsQuery($from, $to)
                ->with(['category:id,name', 'creator:id,name', 'assignees:id,name', 'closedByUser:id,name'])
                ->orderByDesc('updated_at')
                ->chunk(100, function ($tickets) use ($handle) {
                    foreach ($tickets as $ticket) {
                        $closedBy = $ticket->closedByUser?->name ?? $ticket->assignees->first()?->name ?? $ticket->creator?->name ?? '';
                        fputcsv($handle, [
                            __('task_report.export_type_closed_ticket'),
                            '',
                            $ticket->subject,
                            $ticket->category?->name ?? '',
                            '',
                            $closedBy,
                            $ticket->updated_at->format('d/m/Y H:i'),
                            '',
                        ], ';');
                    }
                });

            // Section secondaire : sous-tâches
            $this->baseQuery($from, $to)
                ->with([
                    'ticket:id,subject,ticket_category_id',
                    'ticket.category:id,name',
                    'assignee:id,name',
                    'doneByUser:id,name',
                ])
                ->orderByDesc('ticket_checklist_items.done_at')
                ->chunk(100, function ($items) use ($handle) {
                    foreach ($items as $item) {
                        fputcsv($handle, [
                            __('task_report.export_type_task'),
                            $item->title,
                            $item->ticket?->subject ?? '',
                            $item->ticket?->category?->name ?? '',
                            $item->assignee?->name ?? '',
                            $item->doneByUser?->name ?? '',
                            $item->done_at?->format('d/m/Y H:i') ?? '',
                            $item->due_date?->format('d/m/Y') ?? '',
                        ], ';');
                    }
                });

            fclose($handle);
        }, 'taches-terminees-' . now()->format('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv; charset=utf-8',
        ]);
    }

    public function exportPdf(): StreamedResponse
    {
        [$from, $to] = $this->dateRange();
        $stats = $this->computeStats($from, $to);

        $items = $this->baseQuery($from, $to)
            ->with([
                'ticket:id,subject,ticket_category_id',
                'ticket.category:id,name',
                'assignee:id,name',
                'doneByUser:id,name',
            ])
            ->orderByDesc('ticket_checklist_items.done_at')
            ->limit(500)
            ->get();

        $closedTickets = $this->closedTicketsQuery($from, $to)
            ->with(['category:id,name', 'creator:id,name', 'assignees:id,name', 'closedByUser:id,name'])
            ->orderByDesc('updated_at')
            ->limit(500)
            ->get();

        $org = request()->attributes->get('currentOrganization');
        $orgName = $org?->name ?? '';

        $pdf = Pdf::loadView('pdf.task-report', [
            'stats' => $stats,
            'items' => $items,
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

    public function generateSignedUrl(): void
    {
        if (! $this->canShare()) {
            return;
        }

        [$from, $to] = $this->dateRange();

        $this->lastSignedUrl = URL::temporarySignedRoute(
            'reports.tasks.shared',
            now()->addDays(7),
            [
                'org' => $this->orgId(),
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
            ],
        );
    }

    public function sendShareNotification(): void
    {
        if (! $this->canShare()) {
            return;
        }

        if (! $this->shareUserId || ! $this->lastSignedUrl) {
            return;
        }

        $user = \App\Models\User::find($this->shareUserId);
        if (! $user) {
            return;
        }

        $sender = Auth::user();

        $user->notify(new TaskReportSharedNotification(
            senderName: $sender->name,
            reportUrl: $this->lastSignedUrl,
            orgId: $this->orgId(),
        ));

        $this->showShareModal = false;
        $this->shareUserId = null;

        session()->flash('share_success', __('task_report.share_success'));
    }

    #[Computed]
    public function staffMembers()
    {
        $currentUserId = Auth::id();

        return OrganizationMembership::query()
            ->where('organization_id', $this->orgId())
            ->whereIn('role', ['owner', 'admin', 'agent'])
            ->where('user_id', '!=', $currentUserId)
            ->with('user:id,name')
            ->get()
            ->pluck('user')
            ->filter()
            ->values();
    }

    public function render()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (! $user || ! $user->hasAnyPermission([Permission::ReportsView, Permission::ReportsViewTasks])) {
            abort(403);
        }

        [$from, $to] = $this->dateRange();
        $stats = $this->computeStats($from, $to);

        return view('livewire.reports.task-report', [
            'stats' => $stats,
            'from' => $from,
            'to' => $to,
            'canShare' => $this->canShare(),
            'canViewAll' => $this->canViewAll(),
        ]);
    }
}
