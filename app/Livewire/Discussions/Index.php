<?php

namespace App\Livewire\Discussions;

use App\Enums\TicketMessageType;
use App\Enums\TicketStatus;
use App\Models\DiscussionMessage;
use App\Models\DiscussionThread;
use App\Models\Ticket;
use App\Notifications\TicketNewMessageNotification;
use App\Models\TicketCategory;
use App\Models\TicketMessage;
use App\Models\TicketPriority;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.manexo-app')]
#[Title('Discussions')]
class Index extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $scope = 'tickets'; // threads | tickets

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $viewKey = 'all';

    public bool $showNewDiscussionModal = false;
    public bool $showNewGroupModal = false;
    /** @var int|null Selected user id for 1-to-1 discussion */
    public ?int $newDiscussionUserId = null;
    /** @var int[] Selected user ids for group */
    public array $newGroupUserIds = [];
    public string $newGroupName = '';

    public string $newDiscussionSearch = '';
    public string $newGroupSearch = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function setView(string $key): void
    {
        $allowed = ['direct', 'groups', 'all', 'created_by_me', 'assigned_to_me'];
        if (! in_array($key, $allowed, true)) {
            $key = 'all';
        }
        $this->viewKey = $key;
        $this->resetPage();
    }

    public function setScope(string $scope): void
    {
        $this->scope = in_array($scope, ['threads', 'tickets'], true) ? $scope : 'tickets';
        $this->viewKey = 'all';
        $this->resetPage();
    }

    /**
     * Backward-compat: old UI called setType('threads'|'tickets').
     * Keep it to avoid breaking cached Livewire snapshots.
     */
    public function setType(string $type): void
    {
        $this->setScope($type);
    }

    public function openNewDiscussionModal(): void
    {
        $this->newDiscussionUserId = null;
        $this->newDiscussionSearch = '';
        $this->showNewDiscussionModal = true;
    }

    public function openNewGroupModal(): void
    {
        $this->newGroupUserIds = [];
        $this->newGroupName = '';
        $this->newGroupSearch = '';
        $this->showNewGroupModal = true;
    }

    public function closeNewDiscussionModal(): void
    {
        $this->showNewDiscussionModal = false;
        $this->newDiscussionUserId = null;
        $this->newDiscussionSearch = '';
    }

    public function closeNewGroupModal(): void
    {
        $this->showNewGroupModal = false;
        $this->newGroupUserIds = [];
        $this->newGroupName = '';
        $this->newGroupSearch = '';
    }

    public function createDiscussionWithUser(): void
    {
        $this->validate([
            'newDiscussionUserId' => ['required', 'integer', 'exists:users,id'],
        ]);
        $user = Auth::user();
        $orgId = (int) session('current_organization_id');
        if (! $user || ! $orgId) {
            return;
        }
        $other = User::find($this->newDiscussionUserId);
        if (! $other || ! $other->organizations()->where('organization_id', $orgId)->exists()) {
            $this->addError('newDiscussionUserId', __('Utilisateur invalide.'));
            return;
        }
        // Create a real (non-ticket) discussion thread (1:1).
        $thread = DiscussionThread::create([
            'organization_id' => $orgId,
            'created_by' => $user->id,
            'name' => null,
            'is_group' => false,
        ]);
        $thread->participants()->syncWithoutDetaching([
            $user->id => ['added_by' => $user->id],
            $other->id => ['added_by' => $user->id],
        ]);
        $this->showNewDiscussionModal = false;
        $this->newDiscussionUserId = null;
        $this->redirect(route('discussions.index', ['ticket' => 'd-'.$thread->id]));
    }

    public function createGroupDiscussion(): void
    {
        $this->validate([
            'newGroupUserIds' => ['required', 'array', 'min:1'],
            'newGroupUserIds.*' => ['integer', 'exists:users,id'],
            'newGroupName' => ['nullable', 'string', 'max:255'],
        ]);
        $user = Auth::user();
        $orgId = (int) session('current_organization_id');
        if (! $user || ! $orgId) {
            return;
        }
        $name = trim($this->newGroupName) !== '' ? trim($this->newGroupName) : __('Groupe de discussion');
        $thread = DiscussionThread::create([
            'organization_id' => $orgId,
            'created_by' => $user->id,
            'name' => $name,
            'is_group' => true,
        ]);

        $pivot = [
            $user->id => ['added_by' => $user->id],
        ];
        foreach ($this->newGroupUserIds as $uid) {
            $pivot[$uid] = ['added_by' => $user->id];
        }
        $thread->participants()->syncWithoutDetaching($pivot);
        $this->showNewGroupModal = false;
        $this->newGroupUserIds = [];
        $this->newGroupName = '';
        $this->redirect(route('discussions.index', ['ticket' => 'd-'.$thread->id]));
    }

    /** @return \Illuminate\Contracts\View\View */
    public function render()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $orgId = (int) session('current_organization_id');
        $selectedTicket = null;
        $selectedThread = null;
        $ticketParam = request()->route('ticket');
        if ($ticketParam && $user && $orgId) {
            if (is_string($ticketParam) && str_starts_with($ticketParam, 'd-')) {
                $id = (int) substr($ticketParam, 2);
                $thread = DiscussionThread::query()
                    ->whereKey($id)
                    ->where('organization_id', $orgId)
                    ->first();
                if ($thread && $thread->participants()->where('users.id', $user->id)->exists()) {
                    $selectedThread = $thread;
                    $this->scope = 'threads';
                }
            } else {
                $ticket = $ticketParam instanceof Ticket ? $ticketParam : Ticket::find($ticketParam);
                if ($ticket && (int) $ticket->organization_id === $orgId && $ticket->hasDiscussionAccess((int) $user->id)) {
                    $selectedTicket = $ticket;
                    $this->scope = 'tickets';
                }
            }
        }

        if (! $user || ! $orgId) {
            return view('livewire.discussions.index', [
                'tickets' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20),
                'lastMessages' => collect(),
                'threads' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20),
                'lastThreadMessages' => collect(),
                'viewCounts' => ['all' => 0, 'created_by_me' => 0, 'assigned_to_me' => 0, 'groups' => 0, 'direct' => 0, 'threads_all' => 0],
                'viewKey' => 'all',
                'selectedTicket' => null,
                'selectedThread' => null,
                'orgUsers' => collect(),
                'pendingMessagesCount' => 0,
                'scope' => $this->scope,
            ]);
        }

        /** @var \App\Models\User $user */
        $role = $user->organizations()->where('organization_id', $orgId)->first()?->pivot?->role ?? 'member';
        $isStaff = in_array($role, ['owner', 'admin', 'agent'], true);

        $search = trim($this->search);

        // THREADS (non-ticket) list (only when visible)
        $threads = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
        $lastThreadMessages = collect();
        if ($this->scope === 'threads') {
            $threadsQuery = DiscussionThread::query()
                ->where('organization_id', $orgId)
                ->whereNull('archived_at')
                ->whereHas('participants', fn ($q) => $q->where('users.id', $user->id))
                ->whereHas('messages'); // only started discussions

            if ($this->viewKey === 'groups') {
                $threadsQuery->where('is_group', true);
            } elseif ($this->viewKey === 'direct') {
                $threadsQuery->where('is_group', false);
            }

            if ($search !== '') {
                $threadsQuery->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            }

            $threads = (clone $threadsQuery)
                ->with(['participants:id,name,email'])
                ->orderByDesc('updated_at')
                ->paginate(20);

            if ($threads->isNotEmpty()) {
                $ids = $threads->pluck('id')->all();
                $msgs = DiscussionMessage::query()
                    ->whereIn('thread_id', $ids)
                    ->with('user:id,name')
                    ->orderByDesc('created_at')
                    ->get();
                $lastThreadMessages = $msgs->groupBy('thread_id')->map(fn ($m) => $m->first());
            }
        }

        // TICKETS list (only when visible)
        $tickets = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
        $lastMessages = collect();
        $viewKey = $this->viewKey;
        if ($this->scope === 'tickets') {
            $baseQuery = Ticket::query()
                ->whereUserParticipates((int) $user->id)
                ->where('organization_id', $orgId)
                ->whereNull('archived_at')
                ->whereHas('messages', function ($q) use ($isStaff) {
                    $types = [TicketMessageType::Message->value];
                    if ($isStaff) {
                        $types[] = TicketMessageType::InternalNote->value;
                    }
                    $q->whereIn('type', $types);
                });

            if ($viewKey === 'created_by_me') {
                $baseQuery->where('created_by', $user->id);
            } elseif ($viewKey === 'assigned_to_me') {
                $baseQuery->where('assigned_to', $user->id);
            } elseif ($viewKey === 'groups') {
                $baseQuery->whereHas('participants');
            }

            if ($search !== '') {
                $baseQuery->where(function ($q) use ($search) {
                    $q->where('subject', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%');
                });
            }

            $tickets = (clone $baseQuery)
                ->with(['creator:id,name,email', 'assignee:id,name', 'category:id,name', 'priority:id,name,level'])
                ->orderByDesc('updated_at')
                ->paginate(20);

            if ($tickets->isNotEmpty()) {
                $ids = $tickets->pluck('id')->all();
                $messages = TicketMessage::query()
                    ->whereIn('ticket_id', $ids)
                    ->whereIn('type', $isStaff ? [TicketMessageType::Message->value, TicketMessageType::InternalNote->value] : [TicketMessageType::Message->value])
                    ->with('user:id,name')
                    ->orderByDesc('created_at')
                    ->get();
                $lastMessages = $messages->groupBy('ticket_id')->map(fn ($msgs) => $msgs->first());
            }
        }

        $countsQuery = Ticket::query()
            ->whereUserParticipates((int) $user->id)
            ->where('organization_id', $orgId)
            ->whereNull('archived_at')
            ->whereHas('messages', function ($q) use ($isStaff) {
                $types = [TicketMessageType::Message->value];
                if ($isStaff) {
                    $types[] = TicketMessageType::InternalNote->value;
                }
                $q->whereIn('type', $types);
            });
        $viewCounts = [
            'all' => (clone $countsQuery)->count(),
            'created_by_me' => (clone $countsQuery)->where('created_by', $user->id)->count(),
            'assigned_to_me' => (clone $countsQuery)->where('assigned_to', $user->id)->count(),
            'groups' => (clone $countsQuery)->whereHas('participants')->count(),
        ];

        $threadCountsQuery = DiscussionThread::query()
            ->where('organization_id', $orgId)
            ->whereNull('archived_at')
            ->whereHas('participants', fn ($q) => $q->where('users.id', $user->id))
            ->whereHas('messages');
        $viewCounts['threads_all'] = (clone $threadCountsQuery)->count();
        $viewCounts['direct'] = (clone $threadCountsQuery)->where('is_group', false)->count();
        $viewCounts['thread_groups'] = (clone $threadCountsQuery)->where('is_group', true)->count();

        $orgUsers = User::query()
            ->whereHas('organizations', fn ($q) => $q->where('organization_id', $orgId))
            ->where('id', '!=', $user->id)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $pendingMessagesCount = $user->unreadNotifications()
            ->where('type', TicketNewMessageNotification::class)
            ->count();

        return view('livewire.discussions.index', [
            'threads' => $threads,
            'lastThreadMessages' => $lastThreadMessages,
            'tickets' => $tickets,
            'lastMessages' => $lastMessages,
            'viewCounts' => $viewCounts,
            'viewKey' => $viewKey,
            'selectedTicket' => $selectedTicket,
            'selectedThread' => $selectedThread,
            'orgUsers' => $orgUsers,
            'pendingMessagesCount' => $pendingMessagesCount,
            'scope' => $this->scope,
        ]);
    }
}
