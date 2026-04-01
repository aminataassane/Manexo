<?php

namespace App\Livewire\Tickets;

use App\DataTransferObjects\ClientAssignmentClientScenario;
use App\DataTransferObjects\ClientAssignmentContext;
use App\Enums\Permission;
use App\Enums\TicketMessageType;
use App\Enums\TicketStatus;
use App\Helpers\CacheHelper;
use App\Models\Ticket;
use App\Models\TicketChecklistItem;
use App\Models\TicketGroup;
use App\Models\TicketMessage;
use App\Models\TicketPriority;
use App\Models\User;
use App\Notifications\TicketReopenedNotification;
use App\Notifications\TicketStatusChangedNotification;
use App\Services\AutomationService;
use App\Services\OrganizationAuditService;
use App\Services\SlaService;
use App\Support\TicketClientRoutingNotifier;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.manexo-app')]
#[Title('Tickets')]
class Index extends Component
{
    use WithPagination;

    /** Max tickets per Kanban column (one query per status). */
    private const KANBAN_PER_COLUMN = 50;

    /** Fast path: render list directly without extra init request. */
    public int $loadStage = 2;

    /** Desktop tickets sidebar (quick views / filters). Kept on Livewire to survive morph + Alpine scope issues. */
    public bool $sidebarOpen = true;

    public function toggleSidebar(): void
    {
        $this->sidebarOpen = ! $this->sidebarOpen;
    }

    public function loadPage(): void
    {
        $this->loadTickets();
    }

    public function loadTickets(): void
    {
        if ($this->loadStage >= 2) {
            return;
        }
        $this->loadStage = 2;
    }

    public function mount(): void
    {
        $user = Auth::user();
        abort_if(! $user instanceof User, 403);

        $orgId = (int) session('current_organization_id');
        abort_if(! $orgId, 403);

        // Keep stage loaded to avoid wire:init round-trip latency.
        $this->loadStage = 2;
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

    // Advanced search filters
    #[Url(history: true)]
    public string $dateFrom = '';

    #[Url(history: true)]
    public string $dateTo = '';

    #[Url(history: true)]
    public string $messageSearch = ''; // search inside message content

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
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    public function updatedDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatedDateTo(): void
    {
        $this->resetPage();
    }

    public function updatedMessageSearch(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'status', 'priority', 'assignee', 'source', 'group', 'dateFrom', 'dateTo', 'messageSearch']);
        $this->resetPage();
    }

    // ─── Bulk Actions ────────────────────────────────────────────────

    /** Change status for all selected tickets. */
    public function bulkChangeStatus(string $status): void
    {
        $user = Auth::user();
        if (! $user instanceof User) {
            abort(403);
        }
        $newStatus = TicketStatus::tryFrom($status);
        if (! $newStatus || empty($this->selected)) {
            return;
        }
        $orgId = (int) session('current_organization_id');
        $canChangeStatus = $user->hasPermission(Permission::TicketsChangeStatus);
        if (! $canChangeStatus) {
            abort(403);
        }

        /** @var \Illuminate\Database\Eloquent\Collection<int, Ticket> $tickets */
        $tickets = Ticket::where('organization_id', $orgId)
            ->whereIn('id', $this->selected)
            ->with(['creator:id,name,email', 'organization:id,name'])
            ->get();

        $count = 0;
        foreach ($tickets as $ticket) {
            $oldStatus = $ticket->status;
            if ($oldStatus === $newStatus) {
                continue;
            }
            $isClosed = in_array($newStatus, [TicketStatus::Resolved, TicketStatus::Closed], true);
            $ticket->update([
                'status' => $newStatus,
                'closed_by' => $isClosed ? $user->id : null,
                'closed_at' => $isClosed ? now() : null,
            ]);
            TicketMessage::create([
                'ticket_id' => $ticket->id,
                'user_id' => null,
                'type' => TicketMessageType::System,
                'body' => __('tickets.status_changed', ['actor' => $user->name, 'old' => __('tickets.status.'.$oldStatus->value), 'new' => __('tickets.status.'.$newStatus->value)]),
                'meta' => ['action' => 'status_changed', 'old' => $oldStatus->value, 'new' => $newStatus->value],
            ]);

            // Notify creator
            if ($ticket->created_by && (int) $ticket->created_by !== (int) $user->id) {
                $creator = $ticket->creator;
                if ($creator) {
                    $orgName = $ticket->organization?->name ?? config('app.name', 'Support');
                    $creator->notify(new TicketStatusChangedNotification(
                        ticketId: $ticket->id, ticketPublicId: $ticket->public_id, ticketReference: $ticket->shortReference(),
                        ticketSubject: $ticket->subject, oldStatus: $oldStatus->value, newStatus: $newStatus->value,
                        organizationId: $orgId, organizationName: $orgName,
                    ));
                }
            }
            $count++;
        }

        $this->selected = [];
        $this->selectAll = false;
        CacheHelper::invalidateDashboard($orgId);
        CacheHelper::invalidateReports($orgId);
        CacheHelper::invalidateTicketCounts($orgId);
        session()->flash('tickets_status', __(':count ticket(s) mis à jour.', ['count' => $count]));
    }

    /** Assign all selected tickets to a user. */
    public function bulkAssign(int $userId): void
    {
        $user = Auth::user();
        if (! $user instanceof User || ! $user->hasPermission(Permission::TicketsAssign)) {
            abort(403);
        }
        $orgId = (int) session('current_organization_id');
        if (empty($this->selected)) {
            return;
        }

        $assignee = User::whereKey($userId)->assignableInOrganization($orgId)->firstOrFail();

        /** @var \Illuminate\Database\Eloquent\Collection<int, Ticket> $tickets */
        $tickets = Ticket::where('organization_id', $orgId)
            ->whereIn('id', $this->selected)
            ->with(['assignees:id,name'])
            ->get();

        $count = 0;
        foreach ($tickets as $ticket) {
            $previousResponsible = $ticket->assignees->firstWhere('pivot.role', 'responsible');
            $ticket->assignees()->newPivotQuery()->where('role', 'responsible')->update(['role' => 'collaborator']);
            if ($ticket->assignees()->where('users.id', $userId)->exists()) {
                $ticket->assignees()->updateExistingPivot($userId, ['role' => 'responsible', 'assigned_by' => $user->id]);
            } else {
                $ticket->assignees()->attach($userId, ['assigned_by' => $user->id, 'role' => 'responsible']);
            }
            $ticket->update(['assigned_to' => $userId, 'assigned_by' => $user->id, 'assigned_at' => now()]);
            $auditBody = $previousResponsible && (int) $previousResponsible->id !== $userId
                ? __('tickets.assignment_audit.reassign_responsible', ['actor' => $user->name, 'target' => $assignee->name])
                : __('tickets.assignment_audit.assign_user', ['actor' => $user->name, 'target' => $assignee->name]);
            TicketMessage::create([
                'ticket_id' => $ticket->id,
                'user_id' => null,
                'type' => TicketMessageType::System,
                'body' => $auditBody,
                'meta' => ['action' => 'bulk_assign', 'user_id' => $userId],
            ]);
            TicketClientRoutingNotifier::notify($ticket, new ClientAssignmentContext(
                ClientAssignmentClientScenario::PersonNamed,
                $assignee->name,
            ));
            $count++;
        }

        $this->selected = [];
        $this->selectAll = false;
        CacheHelper::invalidateDashboard($orgId);
        CacheHelper::invalidateReports($orgId);
        CacheHelper::invalidateTicketCounts($orgId);
        session()->flash('tickets_status', __(':count ticket(s) assigné(s) à :name.', ['count' => $count, 'name' => $assignee->name]));
    }

    /** Change priority for all selected tickets. */
    public function bulkChangePriority(int $priorityId): void
    {
        $user = Auth::user();
        if (! $user instanceof User || ! $user->hasPermission(Permission::TicketsChangeStatus)) {
            abort(403);
        }
        $orgId = (int) session('current_organization_id');
        if (empty($this->selected)) {
            return;
        }

        $priority = TicketPriority::where('organization_id', $orgId)->where('is_active', true)->whereKey($priorityId)->firstOrFail();

        $count = Ticket::where('organization_id', $orgId)
            ->whereIn('id', $this->selected)
            ->update(['ticket_priority_id' => $priority->id]);

        $this->selected = [];
        $this->selectAll = false;
        CacheHelper::invalidateDashboard($orgId);
        CacheHelper::invalidateReports($orgId);
        CacheHelper::invalidateTicketCounts($orgId);
        session()->flash('tickets_status', __(':count ticket(s) mis à jour.', ['count' => $count]));
    }

    /** Close all selected tickets. */
    public function bulkClose(): void
    {
        $this->bulkChangeStatus(TicketStatus::Closed->value);
    }

    /** Archive all selected tickets. */
    public function bulkArchive(): void
    {
        $user = Auth::user();
        if (! $user instanceof User || ! $user->hasPermission(Permission::TicketsArchive)) {
            abort(403);
        }
        $orgId = (int) session('current_organization_id');
        if (empty($this->selected)) {
            return;
        }

        $count = Ticket::where('organization_id', $orgId)
            ->whereIn('id', $this->selected)
            ->whereNull('archived_at')
            ->update(['archived_at' => now()]);

        $this->selected = [];
        $this->selectAll = false;
        CacheHelper::invalidateDashboard($orgId);
        CacheHelper::invalidateReports($orgId);
        CacheHelper::invalidateTicketCounts($orgId);
        session()->flash('tickets_status', __(':count ticket(s) archivé(s).', ['count' => $count]));
    }

    public function setDisplayMode(string $mode): void
    {
        $nextMode = in_array($mode, ['list', 'kanban'], true) ? $mode : 'list';
        if ($nextMode === $this->displayMode) {
            return;
        }
        $this->displayMode = $nextMode;
        $this->resetPage();
    }

    public function moveTicket(int $ticketId, string $status): void
    {
        $this->handleMoveTicket($ticketId, $status);
    }

    private function handleMoveTicket(int $ticketId, string $status): void
    {
        $this->processMoveTicket($ticketId, $status);
    }

    private function processMoveTicket(int $ticketId, string $status): void
    {
        $user = Auth::user();
        if (! $user instanceof User) {
            abort(403);
        }

        $orgId = (int) session('current_organization_id');
        if (! $orgId) {
            abort(403);
        }

        $allowed = array_map(fn (TicketStatus $s) => $s->value, TicketStatus::cases());
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
                'old' => __('tickets.status.'.$oldStatus),
                'new' => __('tickets.status.'.$status),
            ]),
            'meta' => ['action' => 'status_changed', 'old' => $oldStatus, 'new' => $status],
        ]);

        OrganizationAuditService::log(
            'ticket.status_changed',
            'ticket',
            (int) $ticket->id,
            ['from' => $oldStatus, 'to' => $status],
        );

        // SLA tracking
        $oldStatusEnum = TicketStatus::tryFrom($oldStatus);
        if ($newStatusEnum === TicketStatus::Pending) {
            SlaService::pause($ticket);
        } elseif ($oldStatusEnum === TicketStatus::Pending && $newStatusEnum !== TicketStatus::Pending) {
            SlaService::resume($ticket);
        }
        if (in_array($newStatusEnum, [TicketStatus::Resolved, TicketStatus::Closed], true)) {
            SlaService::recordResolution($ticket);
        }
        if ($oldStatusEnum && in_array($oldStatusEnum, [TicketStatus::Resolved, TicketStatus::Closed], true)
            && ! in_array($newStatusEnum, [TicketStatus::Resolved, TicketStatus::Closed], true)) {
            SlaService::onReopened($ticket);
            $this->notifyTicketReopened($ticket, (int) $user->id, $user->name);
        }

        CacheHelper::invalidateDashboard($orgId);
        CacheHelper::invalidateReports($orgId);
        CacheHelper::invalidateTicketCounts($orgId);

        AutomationService::evaluate($ticket, 'status_changed');
    }

    private function notifyTicketReopened(Ticket $ticket, int $actorId, string $actorName): void
    {
        $notifyUserIds = collect([(int) $ticket->created_by])
            ->merge($ticket->assignees()->pluck('users.id'))
            ->merge($ticket->participants()->pluck('users.id'))
            ->filter()
            ->unique()
            ->diff([$actorId])
            ->values();
        if ($notifyUserIds->isEmpty()) {
            return;
        }

        /** @var \Illuminate\Database\Eloquent\Collection<int, User> $recipients */
        $recipients = User::query()->whereIn('id', $notifyUserIds)->get();
        foreach ($recipients as $recipient) {
            if ($ticket->hasDiscussionAccess((int) $recipient->id)) {
                $recipient->notify(new TicketReopenedNotification($ticket, $actorId, $actorName));
                event(new \App\Events\UserNotificationReceived((int) $recipient->id, 'ticket_reopened'));
            }
        }
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

        if ($key === $this->viewKey) {
            return;
        }

        $this->viewKey = $key;
        $this->reset(['search', 'status', 'priority', 'assignee']);
        $this->resetPage();
    }

    public function setBox(string $box): void
    {
        $nextBox = in_array($box, ['active', 'archived', 'trash'], true) ? $box : 'active';
        if ($nextBox === $this->box) {
            return;
        }
        $this->box = $nextBox;
        $this->selected = [];
        $this->selectAll = false;
        $this->resetPage();
    }

    public function setSource(string $source): void
    {
        $nextSource = in_array($source, ['all', 'from_form', 'from_platform'], true) ? $source : 'all';
        if ($nextSource === $this->source) {
            return;
        }
        $this->source = $nextSource;
        $this->resetPage();
    }

    /** Restaure un ticket depuis la corbeille (staff uniquement). */
    public function restoreFromTrash(int $ticketId): void
    {
        $user = Auth::user();
        if (! $user instanceof User) {
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
        if (! $user instanceof User) {
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
        return $this->renderTicketsIndex();
    }

    /** @return \Illuminate\Contracts\View\View */
    private function renderTicketsIndex()
    {
        return $this->buildTicketsIndexView();
    }

    private function buildTicketsIndexView()
    {
        $user = Auth::user();
        if (! $user instanceof User) {
            $user = null;
        }
        $orgId = (int) session('current_organization_id');

        $org = $orgId ? request()->attributes->get('currentOrganization') : null;

        $role = $org?->pivot?->role ?? 'member';

        $isStaff = $user ? $user->hasPermission(Permission::TicketsViewAll) : false;

        $viewKey = $this->viewKey === 'my' ? 'assigned_to_me' : $this->viewKey;

        $stageData = $this->buildStageTicketData($user, (int) $orgId, $isStaff, (string) $viewKey);
        $tickets = $stageData['tickets'];
        $kanbanTickets = $stageData['kanbanTickets'];
        $statusColumns = $stageData['statusColumns'];

        // ── Sidebar data (always loaded, even at stage 1) ──

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
            ? Cache::remember("assignable_users:{$orgId}", CacheHelper::TTL, function () use ($orgId) {
                return User::query()->assignableInOrganization($orgId)->orderBy('name')->get(['id', 'name']);
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
                    : ' and (t2.created_by = ? or exists (select 1 from ticket_assignees ta2 where ta2.ticket_id = t2.id and ta2.user_id = ?) or exists (select 1 from ticket_participants tp2 where tp2.ticket_id = t2.id and tp2.user_id = ?))';
                $archivedBindings = $isStaff ? [$orgId] : [$orgId, $user->id, $user->id, $user->id];

                $q = Ticket::query()
                    ->leftJoin('ticket_priorities as tp', 'tickets.ticket_priority_id', '=', 'tp.id')
                    ->where('tickets.organization_id', $orgId)
                    ->whereNull('tickets.archived_at')
                    ->when(! $isStaff, fn ($q) => $q->where(function ($sub) use ($user) {
                        $sub->where('tickets.created_by', $user->id)
                            ->orWhereRaw('exists (select 1 from ticket_assignees where ticket_assignees.ticket_id = tickets.id and ticket_assignees.user_id = ?)', [$user->id])
                            ->orWhereRaw('exists (select 1 from ticket_participants where ticket_participants.ticket_id = tickets.id and ticket_participants.user_id = ?)', [$user->id]);
                    }))
                    ->when($this->group !== '' && $this->group !== 'none', fn ($q) => $q->where('tickets.ticket_group_id', (int) $this->group))
                    ->when($this->group === 'none', fn ($q) => $q->whereNull('tickets.ticket_group_id'))
                    // Stats cards
                    ->selectRaw("count(*) filter (where tickets.status = 'open') as open_count")
                    ->selectRaw("count(*) filter (where tickets.status = 'in_progress') as in_progress_count")
                    ->selectRaw("count(*) filter (where tickets.status = 'pending') as pending_count")
                    ->selectRaw("count(*) filter (where tickets.status in ('resolved','closed') and tickets.updated_at >= ?) as resolved_7d_count", [Carbon::now()->subDays(7)])
                    // View counts
                    ->selectRaw('count(*) as all_count')
                    ->selectRaw('count(*) filter (where tickets.created_by = ?) as created_by_me_count', [$user->id])
                    ->selectRaw('count(*) filter (where exists (select 1 from ticket_assignees where ticket_assignees.ticket_id = tickets.id and ticket_assignees.user_id = ?) or exists (select 1 from ticket_participants where ticket_participants.ticket_id = tickets.id and ticket_participants.user_id = ?)) as assigned_to_me_count', [$user->id, $user->id])
                    ->selectRaw("count(*) filter (where tickets.status in ('open','in_progress','pending') and tickets.updated_at < ?) as past_due_count", [Carbon::now()->subDays(7)])
                    ->selectRaw('count(*) filter (where tp.level >= 3) as high_priority_count')
                    ->selectRaw('count(*) filter (where not exists (select 1 from ticket_assignees where ticket_assignees.ticket_id = tickets.id)) as unassigned_count')
                    // Source counts
                    ->selectRaw('count(*) filter (where exists (select 1 from form_responses fr where fr.ticket_id = tickets.id)) as from_form_count')
                    ->selectRaw('count(*) filter (where not exists (select 1 from form_responses fr where fr.ticket_id = tickets.id)) as from_platform_count')
                    // Archived + trash as subqueries
                    ->selectRaw("(select count(*) from tickets t2 where t2.organization_id = ? and t2.archived_at is not null{$memberFilter}) as archived_count", $archivedBindings);

                if ($canViewTrash) {
                    $q->selectRaw('(select count(*) from tickets t3 where t3.organization_id = ? and t3.deleted_at is not null) as trash_count', [$orgId]);
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

        // Per-group sidebar counts (single lightweight GROUP BY, cached)
        $groupCounts = collect();
        if ($user && $orgId && $ticketGroups->isNotEmpty()) {
            $groupCountsCacheKey = "ticket_group_counts:{$orgId}:".($isStaff ? 'staff' : $user->id);
            $groupCounts = Cache::remember($groupCountsCacheKey, 300, function () use ($orgId, $isStaff, $user) {
                return Ticket::query()
                    ->where('tickets.organization_id', $orgId)
                    ->whereNull('tickets.archived_at')
                    ->whereNotNull('tickets.ticket_group_id')
                    ->when(! $isStaff, fn ($q) => $q->where(function ($sub) use ($user) {
                        $sub->where('tickets.created_by', $user->id)
                            ->orWhereRaw('exists (select 1 from ticket_assignees where ticket_assignees.ticket_id = tickets.id and ticket_assignees.user_id = ?)', [$user->id])
                            ->orWhereRaw('exists (select 1 from ticket_participants where ticket_participants.ticket_id = tickets.id and ticket_participants.user_id = ?)', [$user->id]);
                    }))
                    ->selectRaw('ticket_group_id, count(*) as cnt')
                    ->groupBy('ticket_group_id')
                    ->pluck('cnt', 'ticket_group_id');
            });
        }

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
            sort($ticketIds);
            $checklistCacheKey = sprintf('tickets:checklist_progress:%d:%s', (int) $orgId, md5(implode(',', $ticketIds)));
            $rows = Cache::remember($checklistCacheKey, 60, function () use ($ticketIds) {
                return TicketChecklistItem::query()
                    ->selectRaw('ticket_id, count(*) as total, sum(case when is_done then 1 else 0 end) as done')
                    ->whereIn('ticket_id', $ticketIds)
                    ->groupBy('ticket_id')
                    ->get();
            });
            $checklistProgress = $rows->keyBy('ticket_id');
        }

        return view('livewire.tickets.index', [
            'loadStage' => $this->loadStage,
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

    /**
     * Shared filtered query for the tickets index (list and/or Kanban). Caller must clone before ordering/limit.
     */
    private function buildTicketIndexQuery(?User $user, int $orgId, bool $isStaff, string $viewKey)
    {
        $query = Ticket::query()
            ->select([
                'tickets.id',
                'tickets.public_id',
                'tickets.organization_id',
                'tickets.created_by',
                'tickets.ticket_category_id',
                'tickets.ticket_priority_id',
                'tickets.ticket_group_id',
                'tickets.assigned_to',
                'tickets.status',
                'tickets.subject',
                'tickets.start_date',
                'tickets.due_date',
                'tickets.created_at',
                'tickets.updated_at',
                'tickets.archived_at',
                'tickets.deleted_at',
            ])
            ->with([
                'category:id,name',
                'priority:id,name,level',
                'group:id,name,color',
                'creator:id,name',
                'assignees:id,name',
            ])
            ->where('tickets.organization_id', $orgId);

        $query = $this->applyTicketScopeFilters($query, $user, $isStaff, $viewKey);

        return $this->applyTicketSearchAndAttributeFilters($query);
    }

    private function buildStageTicketData(?User $user, int $orgId, bool $isStaff, string $viewKey): array
    {
        if ($this->loadStage < 2) {
            return [
                'tickets' => new LengthAwarePaginator([], 0, $this->perPage),
                'kanbanTickets' => [],
                'statusColumns' => [],
            ];
        }

        $statusColumns = [
            TicketStatus::Open->value,
            TicketStatus::InProgress->value,
            TicketStatus::Pending->value,
            TicketStatus::Resolved->value,
            TicketStatus::Closed->value,
        ];

        $showKanban = $this->displayMode === 'kanban'
            && $this->box !== 'trash'
            && $user
            && $orgId;

        $baseQuery = $this->buildTicketIndexQuery($user, $orgId, $isStaff, $viewKey);

        if ($showKanban) {
            $kanbanCacheKey = sprintf(
                'tickets:kanban:%d:%d:%s',
                $orgId,
                (int) ($user?->id ?? 0),
                md5($this->filterStateHash($viewKey))
            );
            $kanbanTickets = Cache::remember($kanbanCacheKey, 60, fn () => $this->buildKanbanTicketsPerStatus($baseQuery, $statusColumns));
            $tickets = new LengthAwarePaginator(
                [],
                0,
                max(1, $this->perPage),
                null,
                ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => request()->query()]
            );
        } else {
            $tickets = ($user && $orgId)
                ? (clone $baseQuery)->orderByDesc($this->box === 'trash' ? 'tickets.deleted_at' : 'tickets.updated_at')->paginate($this->perPage)
                : new LengthAwarePaginator([], 0, $this->perPage);
            $kanbanTickets = [];
        }

        return compact('tickets', 'kanbanTickets', 'statusColumns');
    }

    private function filterStateHash(string $viewKey): string
    {
        return implode(':', [
            $this->box,
            $this->displayMode,
            $viewKey,
            trim($this->search),
            $this->status,
            $this->priority,
            $this->assignee,
            $this->group,
            $this->source,
            $this->dateFrom,
            $this->dateTo,
            trim($this->messageSearch),
            (string) $this->perPage,
        ]);
    }

    private function applyTicketScopeFilters($query, ?User $user, bool $isStaff, string $viewKey)
    {
        if ($this->box === 'trash') {
            if (! $isStaff) {
                return $query->whereRaw('1 = 0');
            }

            return $query->onlyTrashed();
        }

        if ($this->box === 'archived') {
            $query->whereNotNull('tickets.archived_at');
        } else {
            $query->whereNull('tickets.archived_at');
        }
        if (! $isStaff && $user) {
            $query->where(function ($q) use ($user) {
                $q->where('tickets.created_by', $user->id)
                    ->orWhereHas('assignees', fn ($a) => $a->where('users.id', $user->id))
                    ->orWhereHas('participants', fn ($p) => $p->where('users.id', $user->id));
            });
        }
        if ($viewKey === 'created_by_me' && $user) {
            $query->where('tickets.created_by', $user->id);
        } elseif ($viewKey === 'assigned_to_me' && $user) {
            $query->where(function ($q) use ($user) {
                $q->whereHas('assignees', fn ($a) => $a->where('users.id', $user->id))
                    ->orWhereHas('participants', fn ($p) => $p->where('users.id', $user->id));
            });
        } elseif ($viewKey === 'past_due') {
            $query->whereIn('tickets.status', ['open', 'in_progress', 'pending'])
                ->where('tickets.updated_at', '<', Carbon::now()->subDays(7));
        } elseif ($viewKey === 'high_priority') {
            $query->whereHas('priority', fn ($p) => $p->where('level', '>=', 3));
        } elseif ($viewKey === 'unassigned') {
            $query->whereDoesntHave('assignees');
        }

        if ($this->source === 'from_form') {
            $query->whereHas('formResponse');
        } elseif ($this->source === 'from_platform') {
            $query->whereDoesntHave('formResponse');
        }
        if ($this->group === 'none') {
            $query->whereNull('tickets.ticket_group_id');
        } elseif ($this->group !== '') {
            $query->where('tickets.ticket_group_id', (int) $this->group);
        }

        return $query;
    }

    private function applyTicketSearchAndAttributeFilters($query)
    {
        $search = trim($this->search);
        if ($search !== '') {
            $searchId = preg_replace('/[^0-9]/', '', $search);
            $query->where(function ($q) use ($search, $searchId) {
                if ($searchId !== '') {
                    $q->orWhere('tickets.id', (int) $searchId);
                }
                $q->orWhere('tickets.subject', 'ilike', "%{$search}%")
                    ->orWhere('tickets.public_id', 'ilike', "%{$search}%")
                    ->orWhereHas('creator', fn ($u) => $u->where('name', 'ilike', "%{$search}%"))
                    ->orWhereHas('assignees', fn ($u) => $u->where('name', 'ilike', "%{$search}%"));
            });
        }

        // Deep search: search inside message content (idx_ticket_messages_body_trgm when pg_trgm is enabled)
        $messageSearch = trim($this->messageSearch);
        if ($messageSearch !== '') {
            $term = '%'.$messageSearch.'%';
            $query->whereExists(function ($sub) use ($term) {
                $sub->selectRaw('1')
                    ->from('ticket_messages')
                    ->whereColumn('ticket_messages.ticket_id', 'tickets.id')
                    ->whereRaw('ticket_messages.body ILIKE ?', [$term]);
            });
        }

        // Date range filter
        if ($this->dateFrom !== '') {
            $query->where('tickets.created_at', '>=', $this->dateFrom.' 00:00:00');
        }
        if ($this->dateTo !== '') {
            $query->where('tickets.created_at', '<=', $this->dateTo.' 23:59:59');
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
                $query->whereHas('assignees', fn ($a) => $a->where('users.id', (int) $this->assignee));
            }
        }

        return $query;
    }

    /**
     * One query per status column (up to KANBAN_PER_COLUMN rows each), same filters as the list.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<\App\Models\Ticket>  $baseQuery
     */
    private function buildKanbanTicketsPerStatus($baseQuery, array $statusColumns): array
    {
        $kanbanTickets = [];
        foreach ($statusColumns as $status) {
            $kanbanTickets[$status] = (clone $baseQuery)
                ->where('tickets.status', $status)
                ->orderByDesc('tickets.updated_at')
                ->limit(self::KANBAN_PER_COLUMN)
                ->get();
        }

        return $kanbanTickets;
    }
}
