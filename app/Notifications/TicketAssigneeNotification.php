<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Models\User;
use App\Notifications\Concerns\BuildsTicketThreadedOutboundMail;
use App\Traits\ResolvesNotificationChannels;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notify when a user is assigned/unassigned/added to a ticket.
 * Internal staff → in-app only. External clients → in-app + email (assignment only).
 */
class TicketAssigneeNotification extends Notification implements ShouldQueue
{
    use BuildsTicketThreadedOutboundMail;
    use Queueable;
    use ResolvesNotificationChannels;

    public function __construct(
        public Ticket $ticket,
        public User $assigner,
        public string $action, // 'assigned' | 'unassigned' | 'participant_added'
    ) {}

    public function via(object $notifiable): array
    {
        // Unassignment and participant_added: always DB only (no email needed)
        if ($this->action !== 'assigned') {
            return ['database'];
        }

        // Assignment: respect internal/external routing
        $orgId = (int) $this->ticket->organization_id;

        return $this->resolveChannels($notifiable, $orgId);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $reference = $this->ticket->shortReference();
        $subject = $reference.' — '.__('Ticket pris en charge');
        $ticketUrl = route('tickets.discussion', ['ticket' => $this->ticket->public_id]);
        $logoUrl = $this->resolveLogoUrl();
        $orgName = $this->ticket->organization?->name ?? config('app.name', 'Support');

        $mail = new MailMessage;
        $mailReplyMode = $this->applyTicketThreadedOutboundMail($mail, $this->ticket, $orgName);

        return $mail
            ->subject($subject)
            ->view('emails.notifications.ticket-status', [
                'subject' => $subject,
                'heading' => __('Ticket pris en charge'),
                'bodyText' => __('Votre demande est prise en charge par notre équipe.'),
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
        $orgId = (int) $this->ticket->organization_id;
        $isInternal = $notifiable instanceof User && $notifiable->isInternalStaff($orgId);

        return [
            'type' => 'ticket_assignee',
            'action' => $this->action,
            'ticket_id' => $this->ticket->id,
            'ticket_public_id' => $this->ticket->public_id,
            'ticket_reference' => $this->ticket->shortReference(),
            'ticket_subject' => $this->ticket->subject,
            // Internal staff sees who assigned; external client sees generic message
            'assigner_id' => $isInternal ? $this->assigner->id : null,
            'assigner_name' => $isInternal ? $this->assigner->name : null,
            'body_excerpt' => $isInternal
                ? __(':name vous a assigné ce ticket.', ['name' => $this->assigner->name])
                : __('Votre demande est prise en charge par notre équipe.'),
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
