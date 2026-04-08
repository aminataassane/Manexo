<?php

namespace App\Livewire\Tickets;

use App\DataTransferObjects\TimelineItem;
use App\Enums\TicketMessageType;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class TicketTimeline extends Component
{
    public int $ticketId;

    public string $ticketPublicId = '';

    public int $ticketCreatorId;

    public bool $canSeeInternalNotes = false;

    /**
     * IDs of messages currently shown, sorted ascending (chronological).
     * Loaded incrementally: newest batch first, then older batches prepended on loadMore.
     *
     * @var list<int>
     */
    public array $loadedMessageIds = [];

    /** Whether there are older messages to load. */
    public bool $hasMoreMessages = false;

    /** Total message count for this ticket (for display). */
    public int $totalCount = 0;

    /** Number of currently loaded messages. */
    public int $loadedCount = 0;

    /** Cached notes count (null = needs refresh). */
    public ?int $notesCount = null;

    private const PAGE_SIZE = 30;

    public function getListeners(): array
    {
        return [
            "echo-private:ticket.{$this->ticketPublicId},.message.sent" => 'onNewMessage',
            "echo-private:ticket.staff.{$this->ticketPublicId},.message.sent" => 'onNewMessage',
            'message-sent' => 'onMessageSent',
        ];
    }

    public function mount(int $ticketId, string $ticketPublicId, int $ticketCreatorId, bool $canSeeInternalNotes): void
    {
        $this->ticketId = $ticketId;
        $this->ticketPublicId = $ticketPublicId;
        $this->ticketCreatorId = $ticketCreatorId;
        $this->canSeeInternalNotes = $canSeeInternalNotes;

        $this->hydrateMessageStats();
    }

    /**
     * Pré-charge compteurs en une requête (PostgreSQL) ou deux max (autres drivers).
     */
    private function hydrateMessageStats(): void
    {
        $internal = TicketMessageType::InternalNote->value;

        if ($this->canSeeInternalNotes && DB::connection()->getDriverName() === 'pgsql') {
            $row = DB::table('ticket_messages')
                ->where('ticket_id', $this->ticketId)
                ->selectRaw('count(*) as total')
                ->selectRaw('count(*) filter (where type = ?) as internal_notes', [$internal])
                ->first();
            $this->totalCount = (int) ($row->total ?? 0);
            $this->notesCount = (int) ($row->internal_notes ?? 0);

            return;
        }

        $this->totalCount = $this->baseQuery()->count();
        $this->notesCount = $this->canSeeInternalNotes
            ? (int) TicketMessage::query()
                ->where('ticket_id', $this->ticketId)
                ->where('type', TicketMessageType::InternalNote)
                ->count()
            : 0;
    }

    /**
     * Called by TicketComposer after sending a message.
     */
    public function onMessageSent(int $messageId = 0): void
    {
        $this->totalCount++;
        $this->notesCount = null;
        if ($messageId > 0) {
            $this->appendLoadedMessageId($messageId);
        }
    }

    /**
     * Called in real-time when a new message arrives via WebSocket.
     */
    public function onNewMessage(array $payload = []): void
    {
        $currentUserId = Auth::user()?->id;
        if (isset($payload['user_id']) && (int) $payload['user_id'] === (int) $currentUserId) {
            return;
        }

        $this->totalCount++;
        $this->notesCount = null;
        if (isset($payload['id'])) {
            $this->appendLoadedMessageId((int) $payload['id']);
        }
        $this->dispatch('new-message-received');
    }

    /**
     * Load older messages (cursor pagination going backwards).
     */
    public function loadMore(): void
    {
        if (! $this->hasMoreMessages || $this->loadedMessageIds === []) {
            return;
        }

        $minId = min($this->loadedMessageIds);
        $batch = $this->baseQuery()
            ->select(['id', 'ticket_id', 'user_id', 'type', 'body', 'attachments', 'meta', 'email_message_id', 'created_at'])
            ->with('user:id,name,email,mention_tag')
            ->where('id', '<', $minId)
            ->orderByDesc('id')
            ->limit(self::PAGE_SIZE + 1)
            ->get();

        $this->hasMoreMessages = $batch->count() > self::PAGE_SIZE;
        $page = $batch->take(self::PAGE_SIZE);
        if ($page->isEmpty()) {
            $this->hasMoreMessages = false;

            return;
        }

        $newIds = $page->sortBy('id')->pluck('id')->map(fn ($id) => (int) $id)->values()->all();
        $this->loadedMessageIds = array_values(array_merge($newIds, $this->loadedMessageIds));
    }

    private function appendLoadedMessageId(int $messageId): void
    {
        if ($messageId <= 0 || in_array($messageId, $this->loadedMessageIds, true)) {
            return;
        }
        $this->loadedMessageIds[] = $messageId;
        sort($this->loadedMessageIds);
    }

    private function baseQuery()
    {
        return TicketMessage::where('ticket_id', $this->ticketId)
            ->when(! $this->canSeeInternalNotes, fn ($q) => $q->where('type', '!=', TicketMessageType::InternalNote));
    }

    /**
     * First batch: most recent PAGE_SIZE messages (newest at the end of the list).
     */
    private function bootstrapInitialBatchIfNeeded(): void
    {
        if ($this->loadedMessageIds !== []) {
            return;
        }

        $messages = $this->baseQuery()
            ->select(['id', 'ticket_id', 'user_id', 'type', 'body', 'attachments', 'meta', 'email_message_id', 'created_at'])
            ->with('user:id,name,email,mention_tag')
            ->orderByDesc('id')
            ->limit(self::PAGE_SIZE + 1)
            ->get();

        $this->hasMoreMessages = $messages->count() > self::PAGE_SIZE;
        $page = $messages->take(self::PAGE_SIZE);
        if ($page->isEmpty()) {
            return;
        }

        $this->loadedMessageIds = $page->sortBy('id')->pluck('id')->map(fn ($id) => (int) $id)->values()->all();
    }

    /**
     * @return Collection<int, TimelineItem>
     */
    private function buildTimelineItems(): Collection
    {
        $this->bootstrapInitialBatchIfNeeded();

        if ($this->loadedMessageIds === []) {
            return collect();
        }

        $messages = TicketMessage::query()
            ->select(['id', 'ticket_id', 'user_id', 'type', 'body', 'attachments', 'meta', 'email_message_id', 'created_at'])
            ->with('user:id,name,email,mention_tag')
            ->whereIn('id', $this->loadedMessageIds)
            ->orderBy('id')
            ->get();

        $ticket = Ticket::query()
            ->select(['id', 'organization_id', 'created_by'])
            ->with(['assignees:id', 'participants:id'])
            ->whereKey($this->ticketId)
            ->firstOrFail();

        $messageUserIds = $messages->pluck('user_id')->unique()->filter()->values();
        $orgRolesByUserId = [];
        if ($messageUserIds->isNotEmpty()) {
            $orgRolesByUserId = DB::table('organization_memberships')
                ->where('organization_id', $ticket->organization_id)
                ->whereIn('user_id', $messageUserIds)
                ->pluck('role', 'user_id')
                ->all();
        }

        $roleLabels = [
            'owner' => __('Admin'),
            'admin' => __('Admin'),
            'agent' => __('Agent'),
            'member' => __('Membre'),
        ];

        $authUserId = (int) (Auth::user()?->id ?? 0);

        return $messages->map(
            fn (TicketMessage $m) => TimelineItem::fromMessage(
                $m,
                $ticket,
                $this->ticketCreatorId,
                $authUserId,
                $orgRolesByUserId,
                $roleLabels,
            )
        );
    }

    public function render()
    {
        $items = $this->buildTimelineItems();
        $this->loadedCount = $items->count();

        $itemsByDate = $items->groupBy('date');
        $noteItems = $items->filter(fn (TimelineItem $i) => $i->isInternal)->groupBy('date');

        if ($this->notesCount === null) {
            $this->notesCount = $this->canSeeInternalNotes
                ? $this->baseQuery()->where('type', TicketMessageType::InternalNote)->count()
                : 0;
        }
        $notesCount = $this->notesCount;

        return view('livewire.tickets.partials.ticket-timeline', [
            'itemsByDate' => $itemsByDate,
            'noteItems' => $noteItems,
            'notesCount' => $notesCount,
            'hasMoreMessages' => $this->hasMoreMessages,
            'canSeeInternalNotes' => $this->canSeeInternalNotes,
            'totalCount' => $this->totalCount,
            'loadedCount' => $this->loadedCount,
        ]);
    }
}
