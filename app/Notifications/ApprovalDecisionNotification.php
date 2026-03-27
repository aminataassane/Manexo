<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ApprovalDecisionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $ticketId,
        public string $ticketPublicId,
        public string $ticketSubject,
        public string $approverName,
        public string $decision,
        public string $comment,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $decisionLabel = $this->decision === 'approved' ? 'approuvé' : 'rejeté';

        return [
            'type' => 'approval_decision',
            'ticket_id' => $this->ticketId,
            'ticket_public_id' => $this->ticketPublicId,
            'ticket_subject' => $this->ticketSubject,
            'approver_name' => $this->approverName,
            'decision' => $this->decision,
            'comment' => $this->comment,
            'message' => "Ticket {$decisionLabel} par {$this->approverName} : {$this->ticketSubject}",
        ];
    }
}
