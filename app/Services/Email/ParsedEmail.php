<?php

namespace App\Services\Email;

class ParsedEmail
{
    public function __construct(
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
        /** @var array<int, array{email: string, name: string|null}> */
        public readonly array $cc = [],
    ) {}

    public function getCleanBody(): string
    {
        $raw = '';
        if ($this->textBody !== null && trim($this->textBody) !== '') {
            $raw = trim($this->textBody);
        } elseif ($this->htmlBody !== null && trim($this->htmlBody) !== '') {
            $raw = trim(EmailParser::sanitizeHtml($this->htmlBody));
        }

        if ($raw === '') {
            return '';
        }

        return EmailParser::stripEmailReplyQuotes($raw);
    }
}
