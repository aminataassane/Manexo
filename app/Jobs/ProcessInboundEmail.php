<?php

namespace App\Jobs;

use App\Models\OrganizationMailbox;
use App\Services\Email\InboundEmailService;
use App\Services\Email\ParsedEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessInboundEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 30;

    public function __construct(
        public readonly int $mailboxId,
        public readonly ?string $messageId,
        public readonly string $fromEmail,
        public readonly ?string $fromName,
        public readonly ?string $subject,
        public readonly ?string $textBody,
        public readonly ?string $htmlBody,
        public readonly ?string $inReplyTo,
        public readonly array $references,
        public readonly array $headers,
        public readonly array $attachments,
        public readonly ?int $uid,
    ) {}

    public function handle(): void
    {
        $mailbox = OrganizationMailbox::query()->find($this->mailboxId);
        if (! $mailbox || ! $mailbox->is_active) {
            return;
        }

        $parsed = new ParsedEmail(
            messageId: $this->messageId,
            fromEmail: $this->fromEmail,
            fromName: $this->fromName,
            subject: $this->subject,
            textBody: $this->textBody,
            htmlBody: $this->htmlBody,
            inReplyTo: $this->inReplyTo,
            references: $this->references,
            headers: $this->headers,
            attachments: $this->attachments,
            uid: $this->uid,
        );

        $service = new InboundEmailService;
        $service->process($parsed, $mailbox);
    }
}
