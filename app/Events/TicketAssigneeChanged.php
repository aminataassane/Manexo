<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class TicketAssigneeChanged implements ShouldBroadcastNow
{
    use Dispatchable;

    public function __construct(
        public int $ticketId,
        public string $ticketSubject,
        public int $affectedUserId,
        public string $action,
        public string $actorName,
        public string $ticketPublicId = '',
    ) {}

    public function broadcastOn(): array
    {
        $ticketChannel = $this->ticketPublicId !== ''
            ? $this->ticketPublicId
            : $this->ticketId;

        return [
            new PrivateChannel('ticket.'.$ticketChannel),
            new PrivateChannel('App.Models.User.'.$this->affectedUserId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'assignee.changed';
    }

    public function broadcastWith(): array
    {
        return [
            'ticket_id' => $this->ticketId,
            'ticket_public_id' => $this->ticketPublicId,
            'ticket_subject' => $this->ticketSubject,
            'affected_user_id' => $this->affectedUserId,
            'action' => $this->action,
            'actor_name' => $this->actorName,
        ];
    }
}
