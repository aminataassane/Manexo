<?php

namespace App\Notifications;

use App\Models\OrganizationMailbox;
use App\Models\TicketMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Email en texte brut, envoi synchrone.
 */
class TicketNewMessageNotification extends Notification
{
    public function __construct(
        public TicketMessage $message
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->message->loadMissing(['ticket.organization.mailbox', 'user']);
        $ticket = $this->message->ticket;
        $sender = $this->message->user;
        $org = $ticket?->organization;
        $mailbox = $org?->mailbox;
        $publicId = $ticket?->public_id ?? '';

        // Threading : collecter les Message-ID existants avant de construire le sujet
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

        // Sujet naturel, pas de [REF-...], juste "Re: " si c'est une reponse
        $ticketSubject = $ticket?->subject ?? 'Nouveau message';
        $subject = ($lastMessageId && ! preg_match('/^Re:\s/i', $ticketSubject))
            ? 'Re: '.$ticketSubject
            : $ticketSubject;

        // Corps HTML du message (conserve la mise en forme)
        $body = $this->message->body;
        $plainBody = strip_tags($body);

        // From : le nom de la personne qui repond
        $senderName = $sender?->name ?? ($org?->name ?? config('app.name', 'Support'));
        $orgName = $org?->name ?? config('app.name', 'Support');

        $mail = (new MailMessage)
            ->subject($subject)
            ->view('emails.notifications.ticket-new', [
                'body' => $body,
                'subject' => $subject,
                'senderName' => $senderName,
                'orgName' => $orgName,
            ]);

        // Pieces jointes du message
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

        // SMTP mailbox : From + Reply-To uniquement si active et configuree
        $useMailboxSmtp = $mailbox && $mailbox->is_active && $mailbox->hasSmtpConfig();

        if ($useMailboxSmtp) {
            $mail->from($mailbox->email, $senderName);

            // Reply-To avec plus-addressing (invisible pour le destinataire)
            if ($publicId && $mailbox->email && str_contains($mailbox->email, '@')) {
                $localPart = substr($mailbox->email, 0, strrpos($mailbox->email, '@'));
                $mailboxDomain = substr($mailbox->email, strrpos($mailbox->email, '@') + 1);
                $replyTo = "{$localPart}+{$publicId}@{$mailboxDomain}";
                $mail->replyTo($replyTo, $senderName);
            }
        }

        $mailboxDomain = null;
        if ($mailbox && $mailbox->email && str_contains($mailbox->email, '@')) {
            $mailboxDomain = substr($mailbox->email, strrpos($mailbox->email, '@') + 1);
        }

        // Message-ID : reutiliser l existant ou en generer un nouveau
        $msgIdHost = $mailboxDomain ?: (parse_url((string) config('app.url'), PHP_URL_HOST) ?: 'localhost');

        if ($this->message->email_message_id) {
            $emailMessageId = $this->message->email_message_id;
        } else {
            $emailMessageId = '<'.Str::uuid().'@'.$msgIdHost.'>';
            $this->message->updateQuietly(['email_message_id' => $emailMessageId]);
        }

        $mail->withSymfonyMessage(function ($symfonyMessage) use ($emailMessageId, $publicId, $lastMessageId, $existingMessageIds, $mailbox, $useMailboxSmtp) {
            $headers = $symfonyMessage->getHeaders();

            // Reference technique (detection de boucles)
            if ($publicId !== '') {
                $headers->addTextHeader('X-Conversation-Ref', $publicId);
            }
            $headers->addIdHeader('Message-ID', trim($emailMessageId, '<>'));

            // Threading via In-Reply-To + References (invisible cote client mail)
            if ($lastMessageId) {
                $headers->addIdHeader('In-Reply-To', trim($lastMessageId, '<>'));
            }
            if (! empty($existingMessageIds)) {
                $refIds = array_map(fn ($id) => trim($id, '<>'), $existingMessageIds);
                $headers->addIdHeader('References', ...$refIds);
            }

            if ($useMailboxSmtp) {
                $headers->addTextHeader('X-Mailbox-Transport-Id', (string) $mailbox->id);
            }
        });

        // Envoi via SMTP mailbox ou fallback
        if ($useMailboxSmtp) {
            $mailerName = $this->registerMailboxMailer($mailbox);
            $mail->mailer($mailerName);

            Log::info('Email notification via mailbox SMTP', [
                'ticket' => $publicId,
                'mailer' => $mailerName,
                'smtp_host' => $mailbox->smtpHost(),
                'from' => $mailbox->email,
            ]);
        } else {
            Log::warning('Email notification via mailer par defaut (SMTP mailbox non configure)', [
                'ticket' => $publicId,
                'default_mailer' => config('mail.default'),
                'mailbox_exists' => (bool) $mailbox,
                'mailbox_active' => $mailbox?->is_active,
                'has_smtp' => $mailbox?->hasSmtpConfig(),
            ]);
        }

        return $mail;
    }

    /**
     * Register a dynamic mailer for the mailbox SMTP and return its name.
     */
    protected function registerMailboxMailer(OrganizationMailbox $mailbox): string
    {
        $mailerName = 'mailbox_'.$mailbox->id;

        $encryption = $mailbox->smtpEncryption();
        $port = $mailbox->smtpPort();

        $scheme = match ($encryption) {
            'ssl' => 'smtps',
            'tls' => ($port === 465) ? 'smtps' : 'smtp',
            default => 'smtp',
        };

        config([
            "mail.mailers.{$mailerName}" => [
                'transport' => 'smtp',
                'scheme' => $scheme,
                'host' => $mailbox->smtpHost(),
                'port' => $port,
                'username' => $mailbox->smtpUsername(),
                'password' => $mailbox->smtpPassword(),
                'timeout' => 30,
            ],
        ]);

        app('mail.manager')->purge($mailerName);

        return $mailerName;
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
