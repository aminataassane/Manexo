<?php

namespace App\Livewire\Tickets;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Orchestrator for the ticket discussion page.
 *
 * All business logic has been extracted to isolated sub-components:
 * - TicketTimeline:  message display, cursor pagination, WebSocket
 * - TicketComposer:  message input, attachments, mentions, send
 * - TicketSidebar:   status, priority, assignees, checklist, edit, archive, delete
 *
 * This component only handles mount, authorization, and layout distribution.
 */
#[Title('Discussion')]
class Discussion extends Component
{
    public int $ticketId;

    public string $ticketPublicId = '';

    /** When true, component is embedded (e.g. in Discussions page) and uses minimal layout. */
    public bool $embedded = false;

    public bool $canSeeInternalNotes = false;

    public bool $canWriteInternalNotes = false;

    public function mount(Ticket $ticket): void
    {
        $this->ticketId = (int) $ticket->id;
        $this->ticketPublicId = $ticket->public_id;

        Gate::authorize('view', $ticket);

        $this->canSeeInternalNotes = Gate::allows('viewInternalNotes', Ticket::class);
        $this->canWriteInternalNotes = Gate::allows('writeInternalNotes', Ticket::class);
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        $layout = $this->embedded ? 'layouts.manexo-embed' : 'layouts.manexo-app';

        $ticket = once(fn () => Ticket::query()
            ->select(['id', 'public_id', 'organization_id', 'created_by', 'ticket_category_id', 'ticket_priority_id', 'ticket_group_id', 'assigned_to', 'status', 'subject', 'description'])
            ->with([
                'creator:id,name,email,mention_tag',
                'assignees:id,name,email,mention_tag',
                'participants:id,name,email,mention_tag',
                'priority:id,name,level',
                'category:id,name',
                'group:id,name,color',
            ])
            ->whereKey($this->ticketId)
            ->where('organization_id', session('current_organization_id'))
            ->firstOrFail());

        /** @var User|null $authUser */
        $authUser = Auth::user();
        $isLocked = $ticket->isLocked();

        // Build mentionable users for the composer (participants + assignees)
        $mentionableUsers = collect([$ticket->creator])
            ->merge($ticket->assignees)
            ->merge($ticket->participants)
            ->filter()
            ->unique('id')
            ->map(fn ($u) => ['id' => $u->id, 'name' => $u->name, 'tag' => $u->mention_tag])
            ->values()
            ->all();

        $view = view('livewire.tickets.discussion', [
            'ticket' => $ticket,
            'ticketPublicId' => $this->ticketPublicId,
            'embedded' => $this->embedded,
            'canSeeInternalNotes' => $this->canSeeInternalNotes,
            'canWriteInternalNotes' => $this->canWriteInternalNotes,
            'mentionableUsers' => $mentionableUsers,
            'isLocked' => $isLocked,
        ]);

        return $view->layout($layout);
    }
}
