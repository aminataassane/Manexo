<?php

namespace App\Livewire\Tickets;

use App\DataTransferObjects\TimelineItem;
use App\Enums\TicketMessageType;
use App\Models\TicketMessage;
use Illuminate\Support\Collection;
use Livewire\Component;

class TicketTimeline extends Component
{
    public int $ticketId;

    public string $ticketPublicId = '';

    public int $ticketCreatorId;

    public bool $canSeeInternalNotes = false;

    /** Oldest loaded message ID — used for cursor-based pagination. */
    public int $oldestLoadedId = PHP_INT_MAX;

    /** Whether there are older messages to load. */
    public bool $hasMoreMessages = false;

    /** Total message count for this ticket (for display). */
    public int $totalCount = 0;

    /** Number of currently loaded messages. */
    public int $loadedCount = 0;

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

        // Count total messages
        $this->totalCount = $this->baseQuery()->count();
    }

    /**
     * Called by TicketComposer after sending a message.
     */
    public function onMessageSent(int $messageId = 0): void
    {
        // Just re-render — the new message will appear via the query
        $this->totalCount = $this->baseQuery()->count();
    }

    /**
     * Called in real-time when a new message arrives via WebSocket.
     */
    public function onNewMessage(array $payload = []): void
    {
        $currentUserId = auth()->id();
        if (isset($payload['user_id']) && (int) $payload['user_id'] === (int) $currentUserId) {
            return;
        }

        $this->totalCount = $this->baseQuery()->count();
        $this->dispatch('new-message-received');
    }

    /**
     * Load older messages (cursor pagination going backwards).
     */
    public function loadMore(): void
    {
        // Will be picked up by render() which always queries from oldestLoadedId
    }

    private function baseQuery()
    {
        return TicketMessage::where('ticket_id', $this->ticketId)
            ->when(! $this->canSeeInternalNotes, fn ($q) => $q->where('type', '!=', TicketMessageType::InternalNote));
    }

    /**
     * Fetch messages for the current page.
     *
     * @return Collection<int, TimelineItem>
     */
    private function fetchMessages(): Collection
    {
        $query = $this->baseQuery()
            ->select(['id', 'ticket_id', 'user_id', 'type', 'body', 'attachments', 'meta', 'email_message_id', 'created_at'])
            ->with('user:id,name,email')
            ->orderByDesc('id');

        // If we have an oldest boundary, fetch from there
        if ($this->oldestLoadedId < PHP_INT_MAX) {
            $query->where('id', '<', $this->oldestLoadedId);
        }

        $messages = $query->limit(self::PAGE_SIZE + 1)->get();

        $this->hasMoreMessages = $messages->count() > self::PAGE_SIZE;
        $page = $messages->take(self::PAGE_SIZE);

        if ($page->isNotEmpty()) {
            $this->oldestLoadedId = (int) $page->last()->id;
        }

        $authUserId = (int) (auth()->id() ?? 0);

        return $page->reverse()->values()->map(
            fn (TicketMessage $m) => TimelineItem::fromMessage($m, $this->ticketCreatorId, $authUserId)
        );
    }

    /**
     * Fetch ALL currently visible messages (from oldest loaded to newest).
     * This is used for the full render to show all loaded messages.
     *
     * @return Collection<int, TimelineItem>
     */
    private function fetchAllLoaded(): Collection
    {
        $query = $this->baseQuery()
            ->select(['id', 'ticket_id', 'user_id', 'type', 'body', 'attachments', 'meta', 'email_message_id', 'created_at'])
            ->with('user:id,name,email');

        // If we've loaded more (oldestLoadedId was pushed back), get everything from there
        if ($this->oldestLoadedId < PHP_INT_MAX) {
            $query->where('id', '>=', $this->oldestLoadedId);
        } else {
            // Initial load: get the last PAGE_SIZE + 1 to detect hasMore
            $messages = $query->orderByDesc('id')->limit(self::PAGE_SIZE + 1)->get();
            $this->hasMoreMessages = $messages->count() > self::PAGE_SIZE;
            $page = $messages->take(self::PAGE_SIZE);

            if ($page->isNotEmpty()) {
                $this->oldestLoadedId = (int) $page->last()->id; // last() is oldest since ordered DESC
            }

            $this->loadedCount = $page->count();
            $authUserId = (int) (auth()->id() ?? 0);

            return $page->reverse()->values()->map(
                fn (TicketMessage $m) => TimelineItem::fromMessage($m, $this->ticketCreatorId, $authUserId)
            );
        }

        $messages = $query->orderBy('id')->get();
        $this->loadedCount = $messages->count();
        $authUserId = (int) (auth()->id() ?? 0);

        return $messages->map(
            fn (TicketMessage $m) => TimelineItem::fromMessage($m, $this->ticketCreatorId, $authUserId)
        );
    }

    public function render()
    {
        $items = $this->fetchAllLoaded();

        // Group by date for display
        $itemsByDate = $items->groupBy('date');

        // Separate notes for the notes tab
        $noteItems = $items->filter(fn (TimelineItem $i) => $i->isInternal)->groupBy('date');

        $notesCount = $this->canSeeInternalNotes
            ? $this->baseQuery()->where('type', TicketMessageType::InternalNote)->count()
            : 0;

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
