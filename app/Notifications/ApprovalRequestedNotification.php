<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ApprovalRequestedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $ticketId,
        public string $ticketPublicId,
        public string $ticketSubject,
        public string $requesterName,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'approval_requested',
            'ticket_id' => $this->ticketId,
            'ticket_public_id' => $this->ticketPublicId,
            'ticket_subject' => $this->ticketSubject,
            'requester_name' => $this->requesterName,
            'message' => "Approbation requise : {$this->ticketSubject} (par {$this->requesterName})",
        ];
    }
}
