<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Notifications\Notification;

class TicketReopenedNotification extends Notification
{
    public function __construct(
        public Ticket $ticket,
        public int $actorId,
        public string $actorName,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'ticket_reopened',
            'ticket_id' => $this->ticket->id,
            'ticket_public_id' => $this->ticket->public_id,
            'ticket_reference' => $this->ticket->shortReference(),
            'ticket_subject' => $this->ticket->subject,
            'actor_id' => $this->actorId,
            'actor_name' => $this->actorName,
            'body_excerpt' => __('Le ticket a été rouvert.'),
        ];
    }
}
