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
use Illuminate\Support\Facades\Cache;
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

    /** Fast path: render lists immediately on first load. */
    public int $loadStage = 2;

    /** Route parameter captured at mount (persists across Livewire AJAX updates). */
    public ?string $selectedParam = null;

    public function mount(): void
    {
        $param = request()->route('discussionParam');
        $this->selectedParam = $param !== null ? (string) $param : null;

        // Canonical URL: /discussions/{TCK-...} for tickets (not numeric ids).
        if ($this->selectedParam !== null && ctype_digit($this->selectedParam)) {
            $ticket = Ticket::find((int) $this->selectedParam);
            if ($ticket?->public_id) {
                $query = request()->query();
                $this->redirect(route('discussions.index', array_merge($query, ['discussionParam' => $ticket->public_id])), navigate: true);

                return;
            }
        }

        // Aligner l'onglet avec l'URL au premier chargement uniquement (pas de ?scope= dans l'URL).
        // Ne pas réécraser le scope à chaque rendu : sinon un fil ouvert (/discussions/d-X) empêche
        // de passer sur l'onglet Tickets (resolveSelected forçait scope=threads).
        if (! request()->query->has('scope') && $this->selectedParam !== null) {
            if (str_starts_with($this->selectedParam, 'd-')) {
                $this->scope = 'threads';
            } else {
                $this->scope = 'tickets';
            }
        }

        // Fast entry: avoid an extra wire:init round-trip before rendering lists.
        $this->loadStage = 2;
    }

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
            $this->redirect(route('discussions.index', ['discussionParam' => 'd-'.$existingThread->id]), navigate: true);

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
        $this->redirect(route('discussions.index', ['discussionParam' => 'd-'.$thread->id]), navigate: true);
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
        $this->redirect(route('discussions.index', ['discussionParam' => 'd-'.$thread->id]), navigate: true);
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
        $ticketParam = $this->selectedParam;

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
            }
        } else {
            $ticket = null;
            if ($ticketParam instanceof Ticket) {
                $ticket = $ticketParam;
            } elseif (is_string($ticketParam)) {
                $ticket = Ticket::where('public_id', $ticketParam)->first();
                if (! $ticket) {
                    $ticket = Ticket::whereRaw('LOWER(public_id) = LOWER(?)', [$ticketParam])->first();
                }
                if (! $ticket && ctype_digit($ticketParam)) {
                    $ticket = Ticket::find((int) $ticketParam);
                }
            }
            if ($ticket && (int) $ticket->organization_id === $orgId && $ticket->hasDiscussionAccess((int) $user->id)) {
                $selectedTicket = $ticket;
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
        $empty = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);

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
                $q->where('name', 'like', '%'.$search.'%');
            });
        }

        $threads = (clone $threadsQuery)
            ->select(['id', 'organization_id', 'name', 'is_group', 'created_by', 'updated_at'])
            ->with(['participants:id,name'])
            ->orderByDesc('updated_at')
            ->paginate(15);

        $lastThreadMessages = collect();
        if ($threads->isNotEmpty()) {
            $ids = $threads->pluck('id')->all();
            // Use a subquery to get only the last message per thread (avoids loading ALL messages)
            $latestIds = DiscussionMessage::query()
                ->whereIn('thread_id', $ids)
                ->selectRaw('max(id) as id')
                ->groupBy('thread_id')
                ->pluck('id');
            $lastThreadMessages = DiscussionMessage::query()
                ->whereIn('id', $latestIds)
                ->with('user:id,name')
                ->get()
                ->keyBy('thread_id');
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
        $empty = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);

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
                $q->where('subject', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
        }

        $tickets = (clone $baseQuery)
            ->select([
                'id',
                'public_id',
                'organization_id',
                'created_by',
                'assigned_to',
                'ticket_category_id',
                'ticket_priority_id',
                'status',
                'source',
                'subject',
                'description',
                'updated_at',
            ])
            ->with(['creator:id,name', 'assignee:id,name', 'category:id,name', 'priority:id,name,level'])
            ->orderByDesc('updated_at')
            ->paginate(15);

        $lastMessages = collect();
        if ($tickets->isNotEmpty()) {
            $ids = $tickets->pluck('id')->all();
            $allowedTypes = $isStaff ? [TicketMessageType::Message->value, TicketMessageType::InternalNote->value] : [TicketMessageType::Message->value];
            // Use subquery to get only the last message per ticket (avoids loading ALL messages)
            $latestIds = TicketMessage::query()
                ->whereIn('ticket_id', $ids)
                ->whereIn('type', $allowedTypes)
                ->selectRaw('max(id) as id')
                ->groupBy('ticket_id')
                ->pluck('id');
            $lastMessages = TicketMessage::query()
                ->whereIn('id', $latestIds)
                ->with('user:id,name')
                ->get()
                ->keyBy('ticket_id');
        }

        return compact('tickets', 'lastMessages');
    }

    /**
     * Build view counts for sidebar badges.
     * Uses two aggregated queries instead of 7 separate COUNTs.
     *
     * @return array<string, int>
     */
    private function buildViewCounts(User $user, int $orgId, bool $isStaff): array
    {
        // ── Ticket counts: single aggregated query ──
        $types = [TicketMessageType::Message->value];
        if ($isStaff) {
            $types[] = TicketMessageType::InternalNote->value;
        }
        $typePlaceholders = implode(',', array_fill(0, count($types), '?'));

        $ticketRow = Ticket::query()
            ->whereUserParticipates((int) $user->id)
            ->where('organization_id', $orgId)
            ->whereNull('archived_at')
            ->whereRaw("exists (select 1 from ticket_messages tm where tm.ticket_id = tickets.id and tm.type in ({$typePlaceholders}))", $types)
            ->selectRaw('count(*) as all_count')
            ->selectRaw('count(*) filter (where created_by = ?) as created_by_me_count', [$user->id])
            ->selectRaw('count(*) filter (where assigned_to = ?) as assigned_to_me_count', [$user->id])
            ->selectRaw('count(*) filter (where exists (select 1 from ticket_participants tp where tp.ticket_id = tickets.id)) as groups_count')
            ->first();

        // ── Thread counts: single aggregated query ──
        $threadRow = DiscussionThread::query()
            ->where('organization_id', $orgId)
            ->whereNull('archived_at')
            ->whereHas('participants', fn ($q) => $q->where('users.id', $user->id))
            ->selectRaw('count(*) as threads_all')
            ->selectRaw('count(*) filter (where is_group = false) as direct_count')
            ->selectRaw('count(*) filter (where is_group = true) as groups_count')
            ->first();

        return [
            'all' => (int) ($ticketRow?->all_count ?? 0),
            'created_by_me' => (int) ($ticketRow?->created_by_me_count ?? 0),
            'assigned_to_me' => (int) ($ticketRow?->assigned_to_me_count ?? 0),
            'groups' => (int) ($ticketRow?->groups_count ?? 0),
            'threads_all' => (int) ($threadRow?->threads_all ?? 0),
            'direct' => (int) ($threadRow?->direct_count ?? 0),
            'thread_groups' => (int) ($threadRow?->groups_count ?? 0),
        ];
    }

    /** @return \Illuminate\Contracts\View\View */
    public function render()
    {
        /** @var User|null $user */
        $user = Auth::user();
        $orgId = (int) session('current_organization_id');

        $emptyState = [
            'tickets' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15),
            'lastMessages' => collect(),
            'threads' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15),
            'lastThreadMessages' => collect(),
            'viewCounts' => ['all' => 0, 'created_by_me' => 0, 'assigned_to_me' => 0, 'groups' => 0, 'direct' => 0, 'threads_all' => 0],
            'viewKey' => 'all',
            'selectedTicket' => null,
            'selectedThread' => null,
            'orgUsers' => collect(),
            'pendingMessagesCount' => 0,
            'unreadCountByThreadId' => collect(),
            'scope' => $this->scope,
        ];

        if (! $user || ! $orgId) {
            return view('livewire.discussions.index', $emptyState);
        }

        if ($this->loadStage < 2) {
            return view('livewire.discussions.index', $emptyState);
        }

        $selected = $this->resolveSelected($user, $orgId);

        $org = request()->attributes->get('currentOrganization');
        $role = $org?->pivot?->role ?? 'member';
        $isStaff = $user->hasPermission(Permission::DiscussionsViewInternalNotes);
        $search = trim($this->search);

        $threadsData = $this->buildThreadsData($user, $orgId, $search);
        $ticketsData = $this->buildTicketsData($user, $orgId, $isStaff, $search);
        $viewCounts = Cache::remember(
            "disc_view_counts:{$orgId}:{$user->id}:".($isStaff ? '1' : '0'),
            120,
            fn () => $this->buildViewCounts($user, $orgId, $isStaff)
        );

        // Load members list only when a "new discussion/group" modal is open.
        // This avoids a heavy query on every Discussions page render.
        $shouldLoadOrgUsers = $this->showNewDiscussionModal || $this->showNewGroupModal;
        $orgUsers = $shouldLoadOrgUsers
            ? Cache::remember(
                "disc_org_users:{$orgId}:{$user->id}",
                300,
                fn () => User::query()
                    ->join('organization_memberships', 'users.id', '=', 'organization_memberships.user_id')
                    ->where('organization_memberships.organization_id', $orgId)
                    ->where('users.id', '!=', $user->id)
                    ->orderBy('users.name')
                    ->get(['users.id', 'users.name', 'users.email'])
            )
            : collect();

        $pendingMessagesCount = Cache::remember(
            "disc_pending_msg:{$user->id}",
            60,
            fn () => $user->unreadNotifications()
                ->where('type', TicketNewMessageNotification::class)
                ->count()
        );

        // Unread-by-thread badges are only needed on conversations scope.
        $unreadCountByThreadId = $this->scope === 'threads'
            ? Cache::remember(
                "disc_unread_threads:{$user->id}",
                60,
                fn () => $user->unreadNotifications()
                    ->whereIn('type', [
                        DiscussionNewMessageNotification::class,
                        DiscussionInviteNotification::class,
                    ])
                    ->select('data')
                    ->get()
                    ->groupBy(fn ($n) => (int) ($n->data['thread_id'] ?? 0))
                    ->map->count()
            )
            : collect();

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
