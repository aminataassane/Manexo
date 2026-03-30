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
 * Notify when a ticket is reopened.
 * Internal staff → in-app only. External clients → in-app + email.
 */
class TicketReopenedNotification extends Notification implements ShouldQueue
{
    use BuildsTicketThreadedOutboundMail;
    use Queueable;
    use ResolvesNotificationChannels;

    public function __construct(
        public Ticket $ticket,
        public int $actorId,
        public string $actorName,
    ) {}

    public function via(object $notifiable): array
    {
        return $this->resolveChannels($notifiable, (int) $this->ticket->organization_id);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $reference = $this->ticket->shortReference();
        $subject = $reference.' — '.__('Ticket rouvert');
        $ticketUrl = route('tickets.discussion', ['ticket' => $this->ticket->public_id]);
        $logoUrl = $this->resolveLogoUrl();
        $orgName = $this->ticket->organization?->name ?? config('app.name', 'Support');

        $mail = new MailMessage;
        $mailReplyMode = $this->applyTicketThreadedOutboundMail($mail, $this->ticket, $orgName);

        return $mail
            ->subject($subject)
            ->view('emails.notifications.ticket-status', [
                'subject' => $subject,
                'heading' => __('Ticket rouvert'),
                'bodyText' => __('Votre ticket a été rouvert suite à une nouvelle activité.'),
                'ticketReference' => $reference,
                'ticketSubject' => $this->ticket->subject,
                'ticketUrl' => $ticketUrl,
                'orgName' => $orgName,
                'logoUrl' => $logoUrl,
                'mailReplyMode' => $mailReplyMode,
            ]);
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

    private function resolveLogoUrl(): ?string
    {
        $org = $this->ticket->organization;
        $branding = is_array($org?->settings) ? ($org->settings['branding'] ?? []) : [];
        $logoPath = $branding['logo_path'] ?? null;

        if ($logoPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($logoPath)) {
            return asset('storage/'.$logoPath);
        }

        return null;
    }
}
