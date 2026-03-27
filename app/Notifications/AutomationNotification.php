<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AutomationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $ticketId,
        public string $ticketPublicId,
        public string $ticketSubject,
        public string $ruleName,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'automation',
            'message' => "Automatisation \"{$this->ruleName}\" exécutée sur le ticket {$this->ticketPublicId}",
            'ticket_id' => $this->ticketId,
            'ticket_public_id' => $this->ticketPublicId,
            'ticket_subject' => $this->ticketSubject,
            'rule_name' => $this->ruleName,
        ];
    }
}
