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
 * Ask the ticket creator to rate their support experience after resolution.
 * Internal staff → in-app only. External clients → in-app + email.
 */
class TicketSatisfactionRequestNotification extends Notification implements ShouldQueue
{
    use BuildsTicketThreadedOutboundMail;
    use Queueable;
    use ResolvesNotificationChannels;

    public function __construct(
        public int $ticketId,
        public string $ticketPublicId,
        public string $ticketReference,
        public string $ticketSubject,
        public int $organizationId,
        public string $organizationName,
    ) {}

    public function via(object $notifiable): array
    {
        return $this->resolveChannels($notifiable, $this->organizationId);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subject = $this->ticketReference.' — '.__('Votre avis nous intéresse');
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
                'heading' => __('Comment s\'est passée votre expérience ?'),
                'bodyText' => __('Votre ticket a été résolu. Nous aimerions connaître votre niveau de satisfaction. Cliquez ci-dessous pour donner votre avis.'),
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
            'type' => 'ticket_satisfaction_request',
            'ticket_id' => $this->ticketId,
            'ticket_public_id' => $this->ticketPublicId,
            'ticket_reference' => $this->ticketReference,
            'ticket_subject' => $this->ticketSubject,
            'body_excerpt' => __('Donnez votre avis sur le traitement de votre ticket.'),
        ];
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
