<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SlaBreachedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $ticketId,
        public string $ticketPublicId,
        public string $ticketSubject,
        public string $slaType,
        public int $minutesOverdue,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $typeLabel = $this->slaType === 'first_response' ? 'Première réponse' : 'Résolution';
        $ticketUrl = url("/tickets/{$this->ticketPublicId}");

        return (new MailMessage)
            ->subject("[SLA] {$typeLabel} en retard : {$this->ticketSubject}")
            ->greeting('Alerte SLA')
            ->line("Le SLA de **{$typeLabel}** pour le ticket **{$this->ticketPublicId}** est en retard de **{$this->minutesOverdue} minute(s)**.")
            ->line("Ticket : {$this->ticketSubject}")
            ->action('Voir le ticket', $ticketUrl)
            ->line('Veuillez traiter ce ticket au plus vite.')
            ->line(__('emails.reply_hint_sla'));
    }

    public function toArray(object $notifiable): array
    {
        $typeLabel = $this->slaType === 'first_response' ? 'Première réponse' : 'Résolution';

        return [
            'type' => 'sla_breached',
            'ticket_id' => $this->ticketId,
            'ticket_public_id' => $this->ticketPublicId,
            'ticket_subject' => $this->ticketSubject,
            'sla_type' => $this->slaType,
            'sla_type_label' => $typeLabel,
            'minutes_overdue' => $this->minutesOverdue,
            'message' => "[SLA] {$typeLabel} en retard : {$this->ticketSubject} (+{$this->minutesOverdue} min)",
        ];
    }
}
