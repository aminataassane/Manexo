<?php

namespace App\Livewire\Discussions;

use App\Enums\Permission;
use App\Enums\TicketMessageType;
use App\Events\UserNotificationReceived;
use App\Models\DiscussionMessage;
use App\Models\DiscussionThread;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use App\Notifications\DiscussionInviteNotification;
use App\Notifications\DiscussionNewMessageNotification;
use App\Notifications\TicketNewMessageNotification;
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

    public function getListeners(): array
    {
        $userId = Auth::id();

        if (! $userId) {
            return [];
        }

        return [
            "echo-private:App.Models.User.{$userId},.notification.received" => '$refresh',
        ];
    }

    #[Url(history: true)]
    public string $scope = 'threads'; // threads = conversations | tickets

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

        // Duplicate 1:1 prevention: find existing thread between these two users
        $existingThread = DiscussionThread::query()
            ->where('organization_id', $orgId)
            ->where('is_group', false)
            ->whereHas('participants', fn ($q) => $q->where('users.id', $user->id))
            ->whereHas('participants', fn ($q) => $q->where('users.id', $other->id))
            ->whereDoesntHave('participants', fn ($q) => $q->whereNotIn('users.id', [$user->id, $other->id]))
            ->first();

        if ($existingThread) {
            $this->showNewDiscussionModal = false;
            $this->newDiscussionUserId = null;
            $this->redirect(route('discussions.index', ['ticket' => 'd-' . $existingThread->id]), navigate: true);
            return;
        }

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

        // Notify the other user
        $other->notify(new DiscussionInviteNotification(
            threadId: $thread->id,
            threadName: $user->name,
            isGroup: false,
            inviterId: $user->id,
            inviterName: $user->name,
        ));
        event(new UserNotificationReceived((int) $other->id, 'discussion_invite'));

        $this->showNewDiscussionModal = false;
        $this->newDiscussionUserId = null;
        $this->redirect(route('discussions.index', ['ticket' => 'd-' . $thread->id]), navigate: true);
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
            $pivot[(int) $uid] = ['added_by' => $user->id];
        }
        $thread->participants()->syncWithoutDetaching($pivot);

        // Notify all invited participants
        foreach ($this->newGroupUserIds as $uid) {
            $invitee = User::find($uid);
            if ($invitee && (int) $invitee->id !== (int) $user->id) {
                $invitee->notify(new DiscussionInviteNotification(
                    threadId: $thread->id,
                    threadName: $name,
                    isGroup: true,
                    inviterId: $user->id,
                    inviterName: $user->name,
                ));
                event(new UserNotificationReceived((int) $invitee->id, 'discussion_invite'));
            }
        }

        $this->showNewGroupModal = false;
        $this->newGroupUserIds = [];
        $this->newGroupName = '';
        $this->redirect(route('discussions.index', ['ticket' => 'd-' . $thread->id]), navigate: true);
    }

    /**
     * Resolve the selected ticket or thread from the route parameter.
     *
     * @return array{selectedTicket: Ticket|null, selectedThread: DiscussionThread|null}
     */
    private function resolveSelected(User $user, int $orgId): array
    {
        $selectedTicket = null;
        $selectedThread = null;
        $ticketParam = request()->route('ticket');

        if (! $ticketParam) {
            return compact('selectedTicket', 'selectedThread');
        }

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

        return compact('selectedTicket', 'selectedThread');
    }

    /**
     * Build the threads paginator and last messages collection.
     *
     * @return array{threads: \Illuminate\Pagination\LengthAwarePaginator<DiscussionThread>, lastThreadMessages: \Illuminate\Support\Collection}
     */
    private function buildThreadsData(User $user, int $orgId, string $search): array
    {
        $empty = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);

        if ($this->scope !== 'threads') {
            return ['threads' => $empty, 'lastThreadMessages' => collect()];
        }

        $threadsQuery = DiscussionThread::query()
            ->where('organization_id', $orgId)
            ->whereNull('archived_at')
            ->whereHas('participants', fn ($q) => $q->where('users.id', $user->id));

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

        $lastThreadMessages = collect();
        if ($threads->isNotEmpty()) {
            $ids = $threads->pluck('id')->all();
            $msgs = DiscussionMessage::query()
                ->whereIn('thread_id', $ids)
                ->with('user:id,name')
                ->orderByDesc('created_at')
                ->get();
            $lastThreadMessages = $msgs->groupBy('thread_id')->map(fn ($m) => $m->first());
        }

        return compact('threads', 'lastThreadMessages');
    }

    /**
     * Build the tickets paginator and last messages collection.
     *
     * @return array{tickets: \Illuminate\Pagination\LengthAwarePaginator<Ticket>, lastMessages: \Illuminate\Support\Collection}
     */
    private function buildTicketsData(User $user, int $orgId, bool $isStaff, string $search): array
    {
        $empty = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);

        if ($this->scope !== 'tickets') {
            return ['tickets' => $empty, 'lastMessages' => collect()];
        }

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

        if ($this->viewKey === 'created_by_me') {
            $baseQuery->where('created_by', $user->id);
        } elseif ($this->viewKey === 'assigned_to_me') {
            $baseQuery->where('assigned_to', $user->id);
        } elseif ($this->viewKey === 'groups') {
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

        $lastMessages = collect();
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

        return compact('tickets', 'lastMessages');
    }

    /**
     * Build view counts for sidebar badges.
     *
     * @return array<string, int>
     */
    private function buildViewCounts(User $user, int $orgId, bool $isStaff): array
    {
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
            ->whereHas('participants', fn ($q) => $q->where('users.id', $user->id));
        $viewCounts['threads_all'] = (clone $threadCountsQuery)->count();
        $viewCounts['direct'] = (clone $threadCountsQuery)->where('is_group', false)->count();
        $viewCounts['thread_groups'] = (clone $threadCountsQuery)->where('is_group', true)->count();

        return $viewCounts;
    }

    /** @return \Illuminate\Contracts\View\View */
    public function render()
    {
        /** @var User|null $user */
        $user = Auth::user();
        $orgId = (int) session('current_organization_id');

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
                'unreadCountByThreadId' => collect(),
                'scope' => $this->scope,
            ]);
        }

        $selected = $this->resolveSelected($user, $orgId);

        $org = request()->attributes->get('currentOrganization');
        $role = $org?->pivot?->role ?? 'member';
        $isStaff = $user->hasPermission(Permission::DiscussionsViewInternalNotes);
        $search = trim($this->search);

        $threadsData = $this->buildThreadsData($user, $orgId, $search);
        $ticketsData = $this->buildTicketsData($user, $orgId, $isStaff, $search);
        $viewCounts = $this->buildViewCounts($user, $orgId, $isStaff);

        $orgUsers = User::query()
            ->whereHas('organizations', fn ($q) => $q->where('organization_id', $orgId))
            ->where('id', '!=', $user->id)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $pendingMessagesCount = $user->unreadNotifications()
            ->where('type', TicketNewMessageNotification::class)
            ->count();

        // Nombre de messages non lus par fil de discussion (pour badge sur chaque discussion)
        $unreadCountByThreadId = $user->unreadNotifications()
            ->whereIn('type', [
                DiscussionNewMessageNotification::class,
                DiscussionInviteNotification::class,
            ])
            ->get()
            ->groupBy(fn ($n) => (int) ($n->data['thread_id'] ?? 0))
            ->map->count();

        return view('livewire.discussions.index', [
            'threads' => $threadsData['threads'],
            'lastThreadMessages' => $threadsData['lastThreadMessages'],
            'tickets' => $ticketsData['tickets'],
            'lastMessages' => $ticketsData['lastMessages'],
            'viewCounts' => $viewCounts,
            'viewKey' => $this->viewKey,
            'selectedTicket' => $selected['selectedTicket'],
            'selectedThread' => $selected['selectedThread'],
            'orgUsers' => $orgUsers,
            'pendingMessagesCount' => $pendingMessagesCount,
            'unreadCountByThreadId' => $unreadCountByThreadId,
            'scope' => $this->scope,
        ]);
    }
}
