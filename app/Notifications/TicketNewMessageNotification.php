<?php

namespace App\Notifications;

use App\Models\TicketMessage;
use App\Notifications\Concerns\BuildsTicketThreadedOutboundMail;
use App\Traits\ResolvesNotificationChannels;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Notify participants when a new message is posted on a ticket.
 *
 * Internal staff (agents, admins) → in-app only.
 * External users (clients, guests) → in-app + email.
 */
class TicketNewMessageNotification extends Notification
{
    use BuildsTicketThreadedOutboundMail;
    use ResolvesNotificationChannels;

    public function __construct(
        public TicketMessage $message
    ) {}

    public function via(object $notifiable): array
    {
        $this->message->loadMissing('ticket');
        $orgId = (int) ($this->message->ticket?->organization_id ?? 0);

        return $this->resolveChannels($notifiable, $orgId);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->message->loadMissing(['ticket.organization.mailbox', 'user']);
        $ticket = $this->message->ticket;
        $sender = $this->message->user;
        $org = $ticket?->organization;
        $publicId = $ticket?->public_id ?? '';

        $existingMessageIds = [];
        if ($ticket) {
            $existingMessageIds = $ticket->messages()
                ->whereNotNull('email_message_id')
                ->where('id', '!=', $this->message->id)
                ->orderBy('created_at')
                ->orderBy('id')
                ->pluck('email_message_id')
                ->values()
                ->all();
        }
        $lastMessageId = ! empty($existingMessageIds) ? end($existingMessageIds) : null;

        $ticketSubject = $ticket?->subject ?? 'Nouveau message';
        $subject = ($lastMessageId && ! preg_match('/^Re:\s/i', $ticketSubject))
            ? 'Re: '.$ticketSubject
            : $ticketSubject;

        $body = $this->message->body;

        $senderName = $sender?->name ?? ($org?->name ?? config('app.name', 'Support'));
        $orgName = $org?->name ?? config('app.name', 'Support');

        $ticketReference = $ticket?->shortReference() ?? '';
        $ticketUrl = $ticket ? route('tickets.discussion', ['ticket' => $publicId]) : '#';

        $logoUrl = null;
        $branding = is_array($org?->settings) ? ($org->settings['branding'] ?? []) : [];
        $logoPath = $branding['logo_path'] ?? null;
        if ($logoPath && Storage::disk('public')->exists($logoPath)) {
            $logoUrl = asset('storage/'.$logoPath);
        }

        $mail = new MailMessage;

        $mailReplyMode = 'use_link';
        if ($ticket) {
            $mailReplyMode = $this->applyTicketThreadedOutboundMail(
                $mail,
                $ticket,
                $senderName,
                $this->message,
                (int) $this->message->id,
            );
        }

        $mail->subject($subject)
            ->view('emails.notifications.ticket-new', [
                'body' => $body,
                'subject' => $subject,
                'senderName' => $senderName,
                'orgName' => $orgName,
                'ticketReference' => $ticketReference,
                'ticketSubject' => $ticket?->subject ?? '',
                'ticketUrl' => $ticketUrl,
                'logoUrl' => $logoUrl,
                'mailReplyMode' => $mailReplyMode,
            ]);

        $attachments = $this->message->attachments;
        if (is_array($attachments) && ! empty($attachments)) {
            $disk = Storage::disk('local');
            foreach ($attachments as $att) {
                $path = $att['path'] ?? null;
                $name = $att['name'] ?? basename($path ?? 'attachment');
                if ($path && $disk->exists($path)) {
                    $mail->attach($disk->path($path), ['as' => $name]);
                }
            }
        }

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        $this->message->loadMissing(['ticket', 'user']);
        $ticket = $this->message->ticket;
        $sender = $this->message->user;

        return [
            'type' => 'ticket_new_message',
            'ticket_id' => $this->message->ticket_id,
            'ticket_public_id' => $ticket?->public_id,
            'ticket_reference' => $ticket?->shortReference(),
            'ticket_subject' => $ticket?->subject,
            'message_id' => $this->message->id,
            'sender_id' => $this->message->user_id,
            'sender_name' => $sender?->name,
            'body_excerpt' => Str::limit(strip_tags($this->message->body), 80),
            'is_internal_note' => $this->message->type->value === 'internal_note',
        ];
    }
}
