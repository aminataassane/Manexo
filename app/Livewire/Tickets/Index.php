<?php

namespace App\Livewire\Tickets;

use App\Enums\TicketStatus;
use App\Models\Organization;
use App\Models\Ticket;
use App\Models\TicketPriority;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
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
    public int $perPage = 10;

    #[Url(history: true)]
    public int $createDrawer = 0;

    public array $selected = [];
    public bool $selectAll = false;
    public bool $showCreateDrawer = false;

    public function mount(): void
    {
        if ($this->createDrawer === 1) {
            $this->showCreateDrawer = true;
        }
    }

    public function updatedShowCreateDrawer($value): void
    {
        if (! $value) {
            $this->createDrawer = 0;
        } else {
            $this->createDrawer = 1;
        }
    }

    public function openCreateDrawer(): void
    {
        $this->showCreateDrawer = true;
        $this->createDrawer = 1;
    }

    public function closeCreateDrawer(): void
    {
        $this->showCreateDrawer = false;
        $this->createDrawer = 0;
    }

    #[On('tickets:closeCreateDrawer')]
    public function closeCreateDrawerFromEvent(): void
    {
        $this->closeCreateDrawer();
    }

    #[On('tickets:created')]
    public function onTicketCreated(): void
    {
        session()->flash('tickets_status', 'Ticket créé avec succès.');
        $this->closeCreateDrawer();
        $this->resetPage();
    }

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
        $this->reset(['search', 'status', 'priority', 'assignee']);
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

        $allowed = array_map(fn (TicketStatus $s) => $s->value, TicketStatus::cases());
        if (! in_array($status, $allowed, true)) {
            return;
        }

        $ticket = Ticket::query()
            ->where('organization_id', $orgId)
            ->whereKey($ticketId)
            ->firstOrFail();

        $role = 'member';
        $org = Organization::query()->find($orgId);
        if ($org) {
            $role = $user->organizations()->whereKey($org->id)->first()?->pivot?->role ?: 'member';
        }

        $isStaff = in_array($role, ['owner', 'admin', 'agent'], true);
        $isCreator = (int) $ticket->created_by === (int) $user->id;
        $isAssignee = (int) ($ticket->assigned_to ?? 0) === (int) $user->id;

        if (! ($isStaff || $isCreator || $isAssignee)) {
            abort(403);
        }

        $ticket->update(['status' => $status]);
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

    public function render()
    {
        $user = Auth::user();
        if (! $user instanceof \App\Models\User) {
            $user = null;
        }
        $orgId = (int) session('current_organization_id');

        $org = $orgId ? Organization::query()->with('users')->find($orgId) : null;

        $role = 'member';
        if ($org && $user) {
            $role = $user->organizations()->whereKey($org->id)->first()?->pivot?->role ?: 'member';
        }

        $isStaff = in_array($role, ['owner', 'admin', 'agent'], true);

        $query = Ticket::query()
            ->with(['category', 'priority', 'creator', 'assignee'])
            ->where('tickets.organization_id', $orgId);

        // Members only see their own tickets
        if (! $isStaff) {
            $query->where('created_by', $user?->id);
        }

        // Left menu "views"
        $viewKey = $this->viewKey === 'my' ? 'assigned_to_me' : $this->viewKey;

        if ($viewKey === 'created_by_me') {
            if ($user) {
                $query->where('tickets.created_by', $user->id);
            }
        } elseif ($viewKey === 'assigned_to_me') {
            if ($user) {
                // Assigned to me
                $query->where('tickets.assigned_to', $user->id);
            }
        } elseif ($viewKey === 'past_due') {
            $query
                ->whereIn('tickets.status', ['open', 'in_progress', 'pending'])
                ->where('tickets.updated_at', '<', Carbon::now()->subDays(7));
        } elseif ($viewKey === 'high_priority') {
            $query->whereHas('priority', fn($p) => $p->where('level', '>=', 3));
        } elseif ($viewKey === 'unassigned') {
            $query->whereNull('tickets.assigned_to');
        }

        $search = trim($this->search);
        if ($search !== '') {
            $searchId = preg_replace('/[^0-9]/', '', $search);

            $query->where(function ($q) use ($search, $searchId) {
                if ($searchId !== '') {
                    $q->orWhere('tickets.id', (int) $searchId);
                }

                $q->orWhere('tickets.subject', 'ilike', "%{$search}%")
                    ->orWhereHas('creator', fn($u) => $u->where('name', 'ilike', "%{$search}%"))
                    ->orWhereHas('assignee', fn($u) => $u->where('name', 'ilike', "%{$search}%"));
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
                $query->whereNull('tickets.assigned_to');
            } else {
                $query->where('tickets.assigned_to', (int) $this->assignee);
            }
        }

        $tickets = ($user && $orgId)
            ? $query->orderByDesc('tickets.updated_at')->paginate($this->perPage)
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
            ? TicketPriority::query()
            ->where('organization_id', $orgId)
            ->where('is_active', true)
            ->orderByDesc('level')
            ->get(['id', 'name', 'level'])
            : collect();

        $assignees = $org
            ? $org->users()->orderBy('name')->get(['users.id', 'users.name'])
            : collect();

        $statsRow = ($user && $orgId)
            ? Ticket::query()
            ->where('tickets.organization_id', $orgId)
            ->when(! $isStaff, fn($q) => $q->where('tickets.created_by', $user->id))
            ->selectRaw("count(*) filter (where status = 'open') as open_count")
            ->selectRaw("count(*) filter (where status = 'in_progress') as in_progress_count")
            ->selectRaw("count(*) filter (where status = 'pending') as pending_count")
            ->selectRaw("count(*) filter (where status in ('resolved','closed') and updated_at >= ?) as resolved_7d_count", [Carbon::now()->subDays(7)])
            ->first()
            : null;

        $stats = [
            'open' => (int) ($statsRow?->open_count ?? 0),
            'in_progress' => (int) ($statsRow?->in_progress_count ?? 0),
            'pending' => (int) ($statsRow?->pending_count ?? 0),
            'resolved_7d' => (int) ($statsRow?->resolved_7d_count ?? 0),
        ];

        $viewsRow = ($user && $orgId)
            ? Ticket::query()
            ->leftJoin('ticket_priorities as tp', 'tickets.ticket_priority_id', '=', 'tp.id')
            ->where('tickets.organization_id', $orgId)
            ->when(! $isStaff, fn($q) => $q->where('tickets.created_by', $user->id))
            ->selectRaw('count(*) as all_count')
            ->selectRaw("count(*) filter (where tickets.created_by = ?) as created_by_me_count", [$user->id])
            ->selectRaw("count(*) filter (where tickets.assigned_to = ?) as assigned_to_me_count", [$user->id])
            ->selectRaw("count(*) filter (where tickets.status in ('open','in_progress','pending') and tickets.updated_at < ?) as past_due_count", [Carbon::now()->subDays(7)])
            ->selectRaw("count(*) filter (where tp.level >= 3) as high_priority_count")
            ->selectRaw("count(*) filter (where tickets.assigned_to is null) as unassigned_count")
            ->first()
            : null;

        $viewCounts = [
            'created_by_me' => (int) ($viewsRow?->created_by_me_count ?? 0),
            'assigned_to_me' => (int) ($viewsRow?->assigned_to_me_count ?? 0),
            'past_due' => (int) ($viewsRow?->past_due_count ?? 0),
            'high_priority' => (int) ($viewsRow?->high_priority_count ?? 0),
            'unassigned' => (int) ($viewsRow?->unassigned_count ?? 0),
            'all' => (int) ($viewsRow?->all_count ?? 0),
        ];

        return view('livewire.tickets.index', [
            'org' => $org,
            'role' => $role,
            'tickets' => $tickets,
            'priorities' => $priorities,
            'assignees' => $assignees,
            'stats' => $stats,
            'viewCounts' => $viewCounts,
            'viewKey' => $viewKey,
            'displayMode' => $this->displayMode,
            'statusColumns' => $statusColumns,
            'kanbanTickets' => $kanbanTickets,
        ]);
    }
}
