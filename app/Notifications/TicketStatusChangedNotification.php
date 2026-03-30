<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Notifications\Concerns\BuildsTicketThreadedOutboundMail;
use App\Traits\ResolvesNotificationChannels;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notify ticket stakeholders when the status changes.
 * Internal staff → in-app only. External clients → in-app + email.
 */
class TicketStatusChangedNotification extends Notification implements ShouldQueue
{
    use BuildsTicketThreadedOutboundMail;
    use Queueable;
    use ResolvesNotificationChannels;

    public function __construct(
        public int $ticketId,
        public string $ticketPublicId,
        public string $ticketReference,
        public string $ticketSubject,
        public string $oldStatus,
        public string $newStatus,
        public int $organizationId,
        public string $organizationName,
    ) {}

    public function via(object $notifiable): array
    {
        return $this->resolveChannels($notifiable, $this->organizationId);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $statusLabel = $this->translatedStatus($this->newStatus);
        $subject = $this->ticketReference.' — '.$statusLabel;
        $bodyText = $this->bodyForStatus($this->newStatus);
        $ticketUrl = route('tickets.discussion', ['ticket' => $this->ticketPublicId]);
        $logoUrl = $this->resolveLogoUrl();

        $mail = new MailMessage;
        $mailReplyMode = 'use_link';
        if ($ticket = Ticket::find($this->ticketId)) {
            $mailReplyMode = $this->applyTicketThreadedOutboundMail($mail, $ticket, $this->organizationName);
        }

        return $mail
            ->subject($subject)
            ->view('emails.notifications.ticket-status', [
                'subject' => $subject,
                'heading' => $statusLabel,
                'bodyText' => $bodyText,
                'ticketReference' => $this->ticketReference,
                'ticketSubject' => $this->ticketSubject,
                'ticketUrl' => $ticketUrl,
                'orgName' => $this->organizationName,
                'logoUrl' => $logoUrl,
                'mailReplyMode' => $mailReplyMode,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'ticket_status_changed',
            'ticket_id' => $this->ticketId,
            'ticket_public_id' => $this->ticketPublicId,
            'ticket_reference' => $this->ticketReference,
            'ticket_subject' => $this->ticketSubject,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'body_excerpt' => $this->translatedStatus($this->newStatus),
        ];
    }

    private function translatedStatus(string $status): string
    {
        return match ($status) {
            'open' => __('tickets.status.open'),
            'in_progress' => __('tickets.status.in_progress'),
            'pending' => __('tickets.status.pending'),
            'resolved' => __('tickets.status.resolved'),
            'closed' => __('tickets.status.closed'),
            default => $status,
        };
    }

    private function bodyForStatus(string $status): string
    {
        return match ($status) {
            'in_progress' => __('Votre demande est en cours de traitement.'),
            'pending' => __('Votre ticket est en attente d\'informations complémentaires.'),
            'resolved' => __('Votre demande a été traitée. Si le problème persiste, vous pouvez répondre à ce message.'),
            'closed' => __('Ce ticket a été clôturé.'),
            'open' => __('Votre ticket est ouvert et sera traité prochainement.'),
            default => __('Le statut de votre ticket a été mis à jour.'),
        };
    }

    private function resolveLogoUrl(): ?string
    {
        $org = \App\Models\Organization::find($this->organizationId);
        $branding = is_array($org?->settings) ? ($org->settings['branding'] ?? []) : [];
        $logoPath = $branding['logo_path'] ?? null;

        if ($logoPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($logoPath)) {
            return asset('storage/'.$logoPath);
        }

        return null;
    }
}
