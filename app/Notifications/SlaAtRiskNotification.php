<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SlaAtRiskNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $ticketId,
        public string $ticketPublicId,
        public string $ticketSubject,
        public string $slaType,
        public int $minutesRemaining,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $typeLabel = $this->slaType === 'first_response' ? 'Première réponse' : 'Résolution';

        return [
            'type' => 'sla_at_risk',
            'ticket_id' => $this->ticketId,
            'ticket_public_id' => $this->ticketPublicId,
            'ticket_subject' => $this->ticketSubject,
            'sla_type' => $this->slaType,
            'sla_type_label' => $typeLabel,
            'minutes_remaining' => $this->minutesRemaining,
            'message' => "[SLA] {$typeLabel} à risque : {$this->ticketSubject} ({$this->minutesRemaining} min restantes)",
        ];
    }
}
