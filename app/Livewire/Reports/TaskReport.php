<?php

namespace App\Livewire\Reports;

use App\Enums\Permission;
use App\Enums\TicketStatus;
use App\Models\OrganizationMembership;
use App\Models\Ticket;
use App\Models\TicketChecklistItem;
use App\Notifications\TaskReportSharedNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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

    public bool $ready = false;

    public function loadPage(): void
    {
        $this->ready = true;
    }

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
        if (! in_array($value, ['today', 'week', 'month', 'custom'], true)) {
            return;
        }
        if ($value === $this->period) {
            return;
        }

        $this->ready = true;
        $this->period = $value;
        $this->resetPage();
        $this->resetPage('closed_page');
        $this->lastSignedUrl = '';
    }

    public function updatedPeriod(string $value): void
    {
        $this->ready = true;
        if (! in_array($value, ['today', 'week', 'month', 'custom'], true)) {
            $this->period = 'week';
        }

        $this->resetPage();
        $this->resetPage('closed_page');
        $this->lastSignedUrl = '';
    }

    public function updatedDateFrom(): void
    {
        $this->ready = true;
        $this->resetPage();
        $this->resetPage('closed_page');
    }

    public function updatedDateTo(): void
    {
        $this->ready = true;
        $this->resetPage();
        $this->resetPage('closed_page');
    }

    public function openShareModal(): void
    {
        if (! $this->canShare()) {
            return;
        }

        $this->showShareModal = true;
    }

    public function closeShareModal(): void
    {
        $this->showShareModal = false;
    }

    /** @return array{Carbon, Carbon} */
    private function dateRange(): array
    {
        if ($this->period === 'custom' && $this->dateFrom && $this->dateTo) {
            try {
                $from = Carbon::parse($this->dateFrom)->startOfDay();
                $to = Carbon::parse($this->dateTo)->endOfDay();

                if ($from->lte($to)) {
                    return [$from, $to];
                }
            } catch (\Throwable) {
                // Fallback to default range below on malformed custom dates.
            }
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
            ->select([
                'ticket_checklist_items.id',
                'ticket_checklist_items.ticket_id',
                'ticket_checklist_items.title',
                'ticket_checklist_items.assigned_to',
                'ticket_checklist_items.done_by',
                'ticket_checklist_items.done_at',
                'ticket_checklist_items.due_date',
            ]);

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
            ->whereBetween('tickets.updated_at', [$from, $to])
            ->select([
                'tickets.id',
                'tickets.public_id',
                'tickets.subject',
                'tickets.ticket_category_id',
                'tickets.created_by',
                'tickets.closed_by',
                'tickets.updated_at',
            ]);

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
     * @return array<string, mixed>
     */
    private function emptyTaskReportStats(): array
    {
        return [
            'closedTicketsCount' => 0,
            'byCategoryClosed' => [],
            'byUserClosed' => [],
            'topCategoryClosed' => null,
            'topUserClosed' => null,
            'tasksTotal' => 0,
            'byUserTasks' => [],
            'byCategoryTasks' => [],
            'topUserTasks' => null,
            'topCategoryTasks' => null,
        ];
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
            ->select([])
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
            ->select([])
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
        if (! $this->ready) {
            return new LengthAwarePaginator([], 0, 20, 1, [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'closed_page',
            ]);
        }

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
        if (! $this->ready) {
            return new LengthAwarePaginator([], 0, 20);
        }

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

    private function getLogoBase64(?object $org): ?string
    {
        if ($org && $org->logo_path) {
            $path = storage_path('app/public/'.ltrim($org->logo_path, '/'));
            if (file_exists($path)) {
                $mime = mime_content_type($path) ?: 'image/png';

                return 'data:'.$mime.';base64,'.base64_encode(file_get_contents($path));
            }
        }

        return null;
    }

    public function exportCsv(): StreamedResponse
    {
        [$from, $to] = $this->dateRange();
        $org = request()->attributes->get('currentOrganization');
        $orgName = $org?->name ?? '';

        return response()->streamDownload(function () use ($from, $to, $orgName) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF"); // UTF-8 BOM

            // Manexo branding header
            fputcsv($handle, ['Manexo — '.$orgName], ';');
            fputcsv($handle, [__('task_report.shared_report_title').' — '.$from->format('d/m/Y').' au '.$to->format('d/m/Y')], ';');
            fputcsv($handle, [__('task_report.report_generated', ['date' => now()->format('d/m/Y H:i')])], ';');
            fputcsv($handle, [], ';');

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
        }, 'taches-terminees-'.now()->format('Y-m-d').'.csv', [
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
        $logoBase64 = $this->getLogoBase64($org);

        $pdf = Pdf::loadView('pdf.task-report', [
            'stats' => $stats,
            'items' => $items,
            'closedTickets' => $closedTickets,
            'from' => $from,
            'to' => $to,
            'orgName' => $orgName,
            'logoBase64' => $logoBase64,
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn () => print $pdf->output(),
            'rapport-taches-'.now()->format('Y-m-d').'.pdf',
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
        if (! $this->ready) {
            return collect();
        }

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

        if (! $this->ready) {
            return view('livewire.reports.task-report', [
                'stats' => $this->emptyTaskReportStats(),
                'from' => $from,
                'to' => $to,
                'canShare' => $this->canShare(),
                'canViewAll' => $this->canViewAll(),
            ]);
        }

        $scope = $this->canViewAll() ? 'all' : 'self';
        $viewerId = (int) Auth::id();
        $statsKey = sprintf(
            'reports:tasks:stats:%d:%s:%s:%s:%s:%d',
            $this->orgId(),
            $this->period,
            $from->toDateString(),
            $to->toDateString(),
            $scope,
            $viewerId
        );
        $stats = Cache::remember($statsKey, 120, fn () => $this->computeStats($from, $to));

        return view('livewire.reports.task-report', [
            'stats' => $stats,
            'from' => $from,
            'to' => $to,
            'canShare' => $this->canShare(),
            'canViewAll' => $this->canViewAll(),
        ]);
    }
}
