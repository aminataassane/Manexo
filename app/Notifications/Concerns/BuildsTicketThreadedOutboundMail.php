<?php

namespace App\Notifications\Concerns;

use App\Models\OrganizationMailbox;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Reply-To plus-addressing, SMTP mailbox, and RFC threading headers for ticket emails.
 */
trait BuildsTicketThreadedOutboundMail
{
    /**
     * Apply From, Reply-To, Message-ID, In-Reply-To, References, and optional dynamic mailer.
     *
     * @param  ?TicketMessage  $persistMessage  When set, Message-ID is stored on this row (new message flow).
     * @param  ?int  $excludeMessageId  Exclude this message id when collecting thread refs (new message flow).
     * @return string mailReplyMode for the Blade partial: reply_email | use_link
     */
    protected function applyTicketThreadedOutboundMail(
        MailMessage $mail,
        Ticket $ticket,
        string $fromDisplayName,
        ?TicketMessage $persistMessage = null,
        ?int $excludeMessageId = null,
    ): string {
        $ticket->loadMissing('organization.mailbox');
        $org = $ticket->organization;
        $mailbox = $org?->mailbox;
        $publicId = (string) ($ticket->public_id ?? '');

        $query = $ticket->messages()
            ->whereNotNull('email_message_id')
            ->orderBy('created_at')
            ->orderBy('id');

        if ($excludeMessageId !== null) {
            $query->where('id', '!=', $excludeMessageId);
        }

        $existingMessageIds = $query->pluck('email_message_id')->values()->all();
        $lastMessageId = ! empty($existingMessageIds) ? end($existingMessageIds) : null;

        $useMailboxSmtp = $mailbox && $mailbox->is_active && $mailbox->hasSmtpConfig();
        $hasTicketReplyTo = $useMailboxSmtp && $publicId !== '' && $mailbox->email && str_contains($mailbox->email, '@');
        $mailReplyMode = $hasTicketReplyTo ? 'reply_email' : 'use_link';

        if ($useMailboxSmtp) {
            $mail->from($mailbox->email, $fromDisplayName);

            if ($publicId !== '' && $mailbox->email && str_contains($mailbox->email, '@')) {
                $localPart = substr($mailbox->email, 0, strrpos($mailbox->email, '@'));
                $mailboxDomain = substr($mailbox->email, strrpos($mailbox->email, '@') + 1);
                $replyTo = "{$localPart}+{$publicId}@{$mailboxDomain}";
                $mail->replyTo($replyTo, $fromDisplayName);
            }
        }

        $mailboxDomain = null;
        if ($mailbox && $mailbox->email && str_contains($mailbox->email, '@')) {
            $mailboxDomain = substr($mailbox->email, strrpos($mailbox->email, '@') + 1);
        }

        $msgIdHost = $mailboxDomain ?: (parse_url((string) config('app.url'), PHP_URL_HOST) ?: 'localhost');

        if ($persistMessage !== null && $persistMessage->email_message_id) {
            $emailMessageId = $persistMessage->email_message_id;
        } elseif ($persistMessage !== null) {
            $emailMessageId = '<'.Str::uuid().'@'.$msgIdHost.'>';
            $meta = is_array($persistMessage->meta) ? $persistMessage->meta : [];
            $meta['email_sent'] = true;
            $persistMessage->updateQuietly([
                'email_message_id' => $emailMessageId,
                'meta' => $meta,
            ]);
        } else {
            $emailMessageId = '<'.Str::uuid().'@'.$msgIdHost.'>';
        }

        $mail->withSymfonyMessage(function ($symfonyMessage) use ($emailMessageId, $publicId, $lastMessageId, $existingMessageIds, $mailbox, $useMailboxSmtp) {
            $headers = $symfonyMessage->getHeaders();

            if ($publicId !== '') {
                $headers->addTextHeader('X-Conversation-Ref', $publicId);
            }
            $headers->addIdHeader('Message-ID', trim($emailMessageId, '<>'));

            if ($lastMessageId) {
                $headers->addIdHeader('In-Reply-To', trim($lastMessageId, '<>'));
            }
            if (! empty($existingMessageIds)) {
                $refIds = array_map(fn ($id) => trim($id, '<>'), $existingMessageIds);
                $headers->addIdHeader('References', ...$refIds);
            }

            if ($useMailboxSmtp && $mailbox) {
                $headers->addTextHeader('X-Mailbox-Transport-Id', (string) $mailbox->id);
            }
        });

        if ($useMailboxSmtp && $mailbox) {
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

        return $mailReplyMode;
    }

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
}
