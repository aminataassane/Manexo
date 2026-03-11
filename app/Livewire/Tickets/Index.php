<?php

namespace App\Livewire\Tickets;

use App\Enums\Permission;
use App\Enums\TicketMessageType;
use App\Enums\TicketStatus;
use App\Helpers\CacheHelper;
use App\Models\Ticket;
use App\Models\TicketGroup;
use App\Models\TicketMessage;
use App\Services\OrganizationAuditService;
use App\Models\TicketChecklistItem;
use App\Models\TicketPriority;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Url;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.manexo-app')]
#[Title('Tickets')]
class Index extends Component
{
    use WithPagination;

    public function mount(): void
    {
        $user = Auth::user();
        abort_if(! $user instanceof \App\Models\User, 403);

        $orgId = (int) session('current_organization_id');
        abort_if(! $orgId, 403);
    }

    public function getListeners(): array
    {
        $userId = Auth::id();

        if (! $userId) {
            return [];
        }

        return [
            "echo-private:App.Models.User.{$userId},.assignee.changed" => '$refresh',
        ];
    }

    #[Url(history: true)]
    public string $box = 'active'; // active | archived

    #[Url(history: true)]
    public string $displayMode = 'list'; // list | kanban

    #[Url(history: true)]
    public string $viewKey = 'all';

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $status = '';

    #[Url(history: true)]
    public string $priority = '';

    #[Url(history: true)]
    public string $assignee = '';

    #[Url(history: true)]
    public string $group = ''; // '' = all, 'none' = no group, or group id

    #[Url(history: true)]
    public string $source = 'all'; // all | from_form | from_platform

    #[Url(history: true)]
    public int $perPage = 10;

    public array $selected = [];
    public bool $selectAll = false;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedDisplayMode(): void
    {
        $this->resetPage();
    }

    public function updatedViewKey(): void
    {
        $this->resetPage();
    }

    public function updatingViewKey(): void
    {
        // When changing view, keep UX consistent
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedPriority(): void
    {
        $this->resetPage();
    }

    public function updatedAssignee(): void
    {
        $this->resetPage();
    }

    public function updatedGroup(): void
    {
        $this->resetPage();
    }

    public function updatedSource(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function updatedSelectAll(bool $value): void
    {
        if (! $value) {
            $this->selected = [];
            return;
        }

        $user = Auth::user();
        $orgId = (int) session('current_organization_id');

        if (! $user || ! $orgId) {
            $this->selected = [];
            return;
        }

        $this->selected = Ticket::query()
            ->where('organization_id', $orgId)
            ->orderByDesc('updated_at')
            ->limit(50)
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->all();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'status', 'priority', 'assignee', 'source', 'group']);
        $this->resetPage();
    }

    public function setDisplayMode(string $mode): void
    {
        $this->displayMode = in_array($mode, ['list', 'kanban'], true) ? $mode : 'list';
        $this->resetPage();
    }

    public function moveTicket(int $ticketId, string $status): void
    {
        $user = Auth::user();
        if (! $user instanceof \App\Models\User) {
            abort(403);
        }

        $orgId = (int) session('current_organization_id');
        if (! $orgId) {
            abort(403);
        }

        $allowed = array_map(fn(TicketStatus $s) => $s->value, TicketStatus::cases());
        if (! in_array($status, $allowed, true)) {
            return;
        }

        $ticket = Ticket::query()
            ->where('organization_id', $orgId)
            ->whereKey($ticketId)
            ->firstOrFail();

        $canChangeStatus = $user->hasPermission(Permission::TicketsChangeStatus);
        $isCreator = (int) $ticket->created_by === (int) $user->id;
        $isAssignee = $ticket->assignees()->where('users.id', $user->id)->exists();

        if (! ($canChangeStatus || $isCreator || $isAssignee)) {
            abort(403);
        }

        $oldStatus = $ticket->status?->value ?? $ticket->status;
        if ($oldStatus === $status) {
            return;
        }

        $newStatusEnum = TicketStatus::from($status);
        $isClosed = in_array($newStatusEnum, [TicketStatus::Resolved, TicketStatus::Closed], true);

        $ticket->update([
            'status' => $status,
            'closed_by' => $isClosed ? $user->id : null,
            'closed_at' => $isClosed ? now() : null,
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => null,
            'type' => TicketMessageType::System,
            'body' => __('tickets.status_changed', [
                'actor' => $user->name,
                'old' => __('tickets.status.' . $oldStatus),
                'new' => __('tickets.status.' . $status),
            ]),
            'meta' => ['action' => 'status_changed', 'old' => $oldStatus, 'new' => $status],
        ]);

        OrganizationAuditService::log(
            'ticket.status_changed',
            'ticket',
            (int) $ticket->id,
            ['from' => $oldStatus, 'to' => $status],
        );

        CacheHelper::invalidateDashboard($orgId);
        CacheHelper::invalidateReports($orgId);
        CacheHelper::invalidateTicketCounts($orgId);
    }

    public function setView(string $key): void
    {
        // Backward-compat alias
        if ($key === 'my') {
            $key = 'assigned_to_me';
        }

        $allowed = ['created_by_me', 'assigned_to_me', 'past_due', 'high_priority', 'unassigned', 'all'];
        if (! in_array($key, $allowed, true)) {
            $key = 'all';
        }

        $this->viewKey = $key;
        $this->reset(['search', 'status', 'priority', 'assignee']);
        $this->resetPage();
    }

    public function setBox(string $box): void
    {
        $this->box = in_array($box, ['active', 'archived', 'trash'], true) ? $box : 'active';
        $this->selected = [];
        $this->selectAll = false;
        $this->resetPage();
    }

    public function setSource(string $source): void
    {
        $this->source = in_array($source, ['all', 'from_form', 'from_platform'], true) ? $source : 'all';
        $this->resetPage();
    }

    /** Restaure un ticket depuis la corbeille (staff uniquement). */
    public function restoreFromTrash(int $ticketId): void
    {
        $user = Auth::user();
        if (! $user instanceof \App\Models\User) {
            abort(403);
        }
        $orgId = (int) session('current_organization_id');
        if (! $orgId) {
            abort(403);
        }
        if (! $user->hasPermission(Permission::TicketsViewTrash)) {
            abort(403);
        }
        $ticket = Ticket::query()
            ->onlyTrashed()
            ->where('organization_id', $orgId)
            ->whereKey($ticketId)
            ->firstOrFail();
        $ticket->restore();

        OrganizationAuditService::log(
            'ticket.restored',
            'ticket',
            (int) $ticket->id,
        );

        session()->flash('tickets_status', __('Ticket restauré.'));
    }

    /** Suppression définitive d'un ticket (forceDelete). Staff avec permission uniquement. */
    public function forceDeleteTicket(int $ticketId): void
    {
        $user = Auth::user();
        if (! $user instanceof \App\Models\User) {
            abort(403);
        }
        $orgId = (int) session('current_organization_id');
        if (! $orgId) {
            abort(403);
        }
        if (! $user->hasPermission(Permission::TicketsDelete)) {
            abort(403);
        }
        $ticket = Ticket::query()
            ->onlyTrashed()
            ->where('organization_id', $orgId)
            ->whereKey($ticketId)
            ->firstOrFail();

        $ticket->forceDelete();

        OrganizationAuditService::log(
            'ticket.force_deleted',
            'ticket',
            $ticketId,
        );

        CacheHelper::invalidateDashboard($orgId);
        CacheHelper::invalidateReports($orgId);
        CacheHelper::invalidateTicketCounts($orgId);

        session()->flash('tickets_status', __('pages.tickets.force_deleted'));
    }

    /** @return \Illuminate\Contracts\View\View */
    public function render()
    {
        $user = Auth::user();
        if (! $user instanceof \App\Models\User) {
            $user = null;
        }
        $orgId = (int) session('current_organization_id');

        $org = $orgId ? request()->attributes->get('currentOrganization') : null;

        $role = $org?->pivot?->role ?? 'member';

        $isStaff = $user ? $user->hasPermission(Permission::TicketsViewAll) : false;

        $query = Ticket::query()
            ->with(['category', 'priority', 'group', 'creator', 'assignees', 'formResponse'])
            ->where('tickets.organization_id', $orgId);

        // Box: Corbeille (soft-deleted) ou Actifs / Archivés
        if ($this->box === 'trash') {
            if (! $isStaff || ! $orgId) {
                $query->whereRaw('1 = 0');
            } else {
                $query->onlyTrashed();
            }
        } else {
            if ($this->box === 'archived') {
                $query->whereNotNull('tickets.archived_at');
            } else {
                $query->whereNull('tickets.archived_at');
            }
            if (! $isStaff && $user) {
                $query->where(function ($q) use ($user) {
                    $q->where('tickets.created_by', $user->id)
                        ->orWhereHas('assignees', fn($a) => $a->where('users.id', $user->id))
                        ->orWhereHas('participants', fn($p) => $p->where('users.id', $user->id));
                });
            }
        }

        // Left menu "views" (ignored in trash box)
        $viewKey = $this->viewKey === 'my' ? 'assigned_to_me' : $this->viewKey;

        if ($this->box !== 'trash') {
            if ($viewKey === 'created_by_me') {
                if ($user) {
                    $query->where('tickets.created_by', $user->id);
                }
            } elseif ($viewKey === 'assigned_to_me') {
                if ($user) {
                    $query->where(function ($q) use ($user) {
                        $q->whereHas('assignees', fn($a) => $a->where('users.id', $user->id))
                            ->orWhereHas('participants', fn($p) => $p->where('users.id', $user->id));
                    });
                }
            } elseif ($viewKey === 'past_due') {
                $query
                    ->whereIn('tickets.status', ['open', 'in_progress', 'pending'])
                    ->where('tickets.updated_at', '<', Carbon::now()->subDays(7));
            } elseif ($viewKey === 'high_priority') {
                $query->whereHas('priority', fn($p) => $p->where('level', '>=', 3));
            } elseif ($viewKey === 'unassigned') {
                $query->whereDoesntHave('assignees');
            }

            // Filtre par source : formulaire ou plateforme
            if ($this->source === 'from_form') {
                $query->whereHas('formResponse');
            } elseif ($this->source === 'from_platform') {
                $query->whereDoesntHave('formResponse');
            }

            // Filtre par groupe
            if ($this->group === 'none') {
                $query->whereNull('tickets.ticket_group_id');
            } elseif ($this->group !== '') {
                $query->where('tickets.ticket_group_id', (int) $this->group);
            }
        }

        $search = trim($this->search);
        if ($search !== '') {
            $searchId = preg_replace('/[^0-9]/', '', $search);

            $query->where(function ($q) use ($search, $searchId) {
                if ($searchId !== '') {
                    $q->orWhere('tickets.id', (int) $searchId);
                }

                $q->orWhere('tickets.subject', 'ilike', "%{$search}%")
                    ->orWhere('tickets.public_id', 'ilike', "%{$search}%")
                    ->orWhereHas('creator', fn($u) => $u->where('name', 'ilike', "%{$search}%"))
                    ->orWhereHas('assignees', fn($u) => $u->where('name', 'ilike', "%{$search}%"));
            });
        }

        if ($this->status !== '') {
            $query->where('tickets.status', $this->status);
        }

        if ($this->priority !== '') {
            $query->where('tickets.ticket_priority_id', (int) $this->priority);
        }

        if ($this->assignee !== '') {
            if ($this->assignee === 'unassigned') {
                $query->whereDoesntHave('assignees');
            } else {
                $query->whereHas('assignees', fn($a) => $a->where('users.id', (int) $this->assignee));
            }
        }

        $tickets = ($user && $orgId)
            ? $query->orderByDesc($this->box === 'trash' ? 'tickets.deleted_at' : 'tickets.updated_at')->paginate($this->perPage)
            : new LengthAwarePaginator([], 0, $this->perPage);

        $statusColumns = [
            TicketStatus::Open->value,
            TicketStatus::InProgress->value,
            TicketStatus::Pending->value,
            TicketStatus::Resolved->value,
            TicketStatus::Closed->value,
        ];

        $kanbanTickets = [];
        if ($this->displayMode === 'kanban' && $user && $orgId) {
            $rows = (clone $query)
                ->orderByDesc('tickets.updated_at')
                ->limit(300)
                ->get();

            foreach ($statusColumns as $s) {
                $kanbanTickets[$s] = [];
            }

            foreach ($rows as $t) {
                $key = $t->status?->value ?? 'open';
                if (! isset($kanbanTickets[$key])) {
                    $kanbanTickets[$key] = [];
                }
                $kanbanTickets[$key][] = $t;
            }
        }

        $priorities = $orgId
            ? Cache::remember(CacheHelper::prioritiesKey($orgId, true), CacheHelper::TTL, function () use ($orgId) {
                return TicketPriority::query()
                    ->where('organization_id', $orgId)
                    ->where('is_active', true)
                    ->orderByDesc('level')
                    ->get(['id', 'name', 'level']);
            })
            : collect();

        $assignees = ($org && $orgId)
            ? Cache::remember(CacheHelper::membersKey($orgId), CacheHelper::TTL, function () use ($org) {
                return $org->users()->orderBy('name')->get(['users.id', 'users.name']);
            })
            : collect();

        $ticketGroups = $orgId
            ? Cache::remember(CacheHelper::ticketGroupsKey($orgId, true), CacheHelper::TTL, function () use ($orgId) {
                return TicketGroup::query()
                    ->where('organization_id', $orgId)
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->get(['id', 'name', 'slug', 'color']);
            })
            : collect();

        // ── Single combined query: stats + view counts + source counts (cached) ──
        $canViewTrash = $user && $user->hasPermission(Permission::TicketsViewTrash) && $orgId;

        $countsRow = null;
        if ($user && $orgId) {
            $countsCacheKey = CacheHelper::ticketCountsKey($orgId, (int) $user->id, $this->group);

            $countsRow = Cache::remember($countsCacheKey, CacheHelper::TTL, function () use ($orgId, $user, $isStaff, $canViewTrash) {
                $memberFilter = $isStaff
                    ? ''
                    : " and (t2.created_by = ? or exists (select 1 from ticket_assignees ta2 where ta2.ticket_id = t2.id and ta2.user_id = ?) or exists (select 1 from ticket_participants tp2 where tp2.ticket_id = t2.id and tp2.user_id = ?))";
                $archivedBindings = $isStaff ? [$orgId] : [$orgId, $user->id, $user->id, $user->id];

                $q = Ticket::query()
                    ->leftJoin('ticket_priorities as tp', 'tickets.ticket_priority_id', '=', 'tp.id')
                    ->where('tickets.organization_id', $orgId)
                    ->whereNull('tickets.archived_at')
                    ->when(! $isStaff, fn($q) => $q->where(function ($sub) use ($user) {
                        $sub->where('tickets.created_by', $user->id)
                            ->orWhereRaw('exists (select 1 from ticket_assignees where ticket_assignees.ticket_id = tickets.id and ticket_assignees.user_id = ?)', [$user->id])
                            ->orWhereRaw('exists (select 1 from ticket_participants where ticket_participants.ticket_id = tickets.id and ticket_participants.user_id = ?)', [$user->id]);
                    }))
                    ->when($this->group !== '' && $this->group !== 'none', fn($q) => $q->where('tickets.ticket_group_id', (int) $this->group))
                    ->when($this->group === 'none', fn($q) => $q->whereNull('tickets.ticket_group_id'))
                    // Stats cards
                    ->selectRaw("count(*) filter (where tickets.status = 'open') as open_count")
                    ->selectRaw("count(*) filter (where tickets.status = 'in_progress') as in_progress_count")
                    ->selectRaw("count(*) filter (where tickets.status = 'pending') as pending_count")
                    ->selectRaw("count(*) filter (where tickets.status in ('resolved','closed') and tickets.updated_at >= ?) as resolved_7d_count", [Carbon::now()->subDays(7)])
                    // View counts
                    ->selectRaw('count(*) as all_count')
                    ->selectRaw("count(*) filter (where tickets.created_by = ?) as created_by_me_count", [$user->id])
                    ->selectRaw("count(*) filter (where exists (select 1 from ticket_assignees where ticket_assignees.ticket_id = tickets.id and ticket_assignees.user_id = ?) or exists (select 1 from ticket_participants where ticket_participants.ticket_id = tickets.id and ticket_participants.user_id = ?)) as assigned_to_me_count", [$user->id, $user->id])
                    ->selectRaw("count(*) filter (where tickets.status in ('open','in_progress','pending') and tickets.updated_at < ?) as past_due_count", [Carbon::now()->subDays(7)])
                    ->selectRaw("count(*) filter (where tp.level >= 3) as high_priority_count")
                    ->selectRaw("count(*) filter (where not exists (select 1 from ticket_assignees where ticket_assignees.ticket_id = tickets.id)) as unassigned_count")
                    // Source counts
                    ->selectRaw("count(*) filter (where exists (select 1 from form_responses fr where fr.ticket_id = tickets.id)) as from_form_count")
                    ->selectRaw("count(*) filter (where not exists (select 1 from form_responses fr where fr.ticket_id = tickets.id)) as from_platform_count")
                    // Archived + trash as subqueries
                    ->selectRaw("(select count(*) from tickets t2 where t2.organization_id = ? and t2.archived_at is not null{$memberFilter}) as archived_count", $archivedBindings);

                if ($canViewTrash) {
                    $q->selectRaw("(select count(*) from tickets t3 where t3.organization_id = ? and t3.deleted_at is not null) as trash_count", [$orgId]);
                }

                return $q->first();
            });
        }

        $stats = [
            'open' => (int) ($countsRow?->open_count ?? 0),
            'in_progress' => (int) ($countsRow?->in_progress_count ?? 0),
            'pending' => (int) ($countsRow?->pending_count ?? 0),
            'resolved_7d' => (int) ($countsRow?->resolved_7d_count ?? 0),
        ];

        $viewCounts = [
            'created_by_me' => (int) ($countsRow?->created_by_me_count ?? 0),
            'assigned_to_me' => (int) ($countsRow?->assigned_to_me_count ?? 0),
            'past_due' => (int) ($countsRow?->past_due_count ?? 0),
            'high_priority' => (int) ($countsRow?->high_priority_count ?? 0),
            'unassigned' => (int) ($countsRow?->unassigned_count ?? 0),
            'all' => (int) ($countsRow?->all_count ?? 0),
            'archived' => (int) ($countsRow?->archived_count ?? 0),
            'trash' => (int) ($countsRow?->trash_count ?? 0),
            'from_form' => (int) ($countsRow?->from_form_count ?? 0),
            'from_platform' => (int) ($countsRow?->from_platform_count ?? 0),
        ];

        // Per-group sidebar counts (single lightweight GROUP BY)
        $groupCounts = ($user && $orgId && $ticketGroups->isNotEmpty())
            ? Ticket::query()
                ->where('tickets.organization_id', $orgId)
                ->whereNull('tickets.archived_at')
                ->whereNotNull('tickets.ticket_group_id')
                ->when(! $isStaff, fn($q) => $q->where(function ($sub) use ($user) {
                    $sub->where('tickets.created_by', $user->id)
                        ->orWhereRaw('exists (select 1 from ticket_assignees where ticket_assignees.ticket_id = tickets.id and ticket_assignees.user_id = ?)', [$user->id])
                        ->orWhereRaw('exists (select 1 from ticket_participants where ticket_participants.ticket_id = tickets.id and ticket_participants.user_id = ?)', [$user->id]);
                }))
                ->selectRaw('ticket_group_id, count(*) as cnt')
                ->groupBy('ticket_group_id')
                ->pluck('cnt', 'ticket_group_id')
            : collect();

        // Active group model for header display
        $activeGroup = null;
        if ($this->group !== '' && $this->group !== 'none' && $ticketGroups->isNotEmpty()) {
            $activeGroup = $ticketGroups->firstWhere('id', (int) $this->group);
        }

        $ticketIds = $tickets->pluck('id')->values()->all();
        if ($this->displayMode === 'kanban' && ! empty($kanbanTickets)) {
            $kanbanIds = collect($kanbanTickets)->flatten(1)->pluck('id')->unique()->values()->all();
            $ticketIds = array_values(array_unique(array_merge($ticketIds, $kanbanIds)));
        }
        $checklistProgress = collect();
        if (! empty($ticketIds)) {
            $rows = TicketChecklistItem::query()
                ->selectRaw('ticket_id, count(*) as total, sum(case when is_done then 1 else 0 end) as done')
                ->whereIn('ticket_id', $ticketIds)
                ->groupBy('ticket_id')
                ->get();
            $checklistProgress = $rows->keyBy('ticket_id');
        }

        return view('livewire.tickets.index', [
            'org' => $org,
            'role' => $role,
            'isStaff' => $isStaff,
            'tickets' => $tickets,
            'priorities' => $priorities,
            'assignees' => $assignees,
            'stats' => $stats,
            'viewCounts' => $viewCounts,
            'viewKey' => $viewKey,
            'displayMode' => $this->displayMode,
            'statusColumns' => $statusColumns,
            'kanbanTickets' => $kanbanTickets,
            'box' => $this->box,
            'source' => $this->source,
            'ticketGroups' => $ticketGroups,
            'groupCounts' => $groupCounts,
            'activeGroup' => $activeGroup,
            'checklistProgress' => $checklistProgress,
        ]);
    }
}
