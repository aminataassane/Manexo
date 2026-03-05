<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TicketAssigneeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Ticket $ticket,
        public User $assigner,
        public string $action, // 'assigned' | 'unassigned'
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'ticket_assignee',
            'action' => $this->action,
            'ticket_id' => $this->ticket->id,
            'ticket_public_id' => $this->ticket->public_id,
            'ticket_reference' => $this->ticket->shortReference(),
            'ticket_subject' => $this->ticket->subject,
            'assigner_id' => $this->assigner->id,
            'assigner_name' => $this->assigner->name,
        ];
    }
}
