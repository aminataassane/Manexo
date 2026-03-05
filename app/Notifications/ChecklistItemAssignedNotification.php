<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Models\TicketChecklistItem;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ChecklistItemAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public TicketChecklistItem $item,
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
            'type' => 'checklist_item_assigned',
            'action' => $this->action,
            'ticket_id' => $this->ticket->id,
            'ticket_public_id' => $this->ticket->public_id,
            'ticket_reference' => $this->ticket->shortReference(),
            'ticket_subject' => $this->ticket->subject,
            'item_id' => $this->item->id,
            'item_title' => $this->item->title,
            'assigner_id' => $this->assigner->id,
            'assigner_name' => $this->assigner->name,
        ];
    }
}
