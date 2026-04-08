<?php

namespace Tests\Unit\Email;

use App\Services\Email\ParsedEmail;
use PHPUnit\Framework\TestCase;

class ParsedEmailGetCleanBodyTest extends TestCase
{
    public function test_get_clean_body_prefers_plain_text(): void
    {
        $email = new ParsedEmail(
            messageId: null,
            fromEmail: 'a@b.com',
            fromName: null,
            subject: 'S',
            textBody: "Line one\nLine two",
            htmlBody: '<p>HTML ignored when text present</p>',
            inReplyTo: null,
            references: [],
            headers: [],
            attachments: [],
            uid: null,
        );

        $this->assertStringContainsString('Line one', $email->getCleanBody());
        $this->assertStringNotContainsString('HTML ignored', $email->getCleanBody());
    }

    public function test_get_clean_body_falls_back_to_sanitized_html_when_no_text(): void
    {
        $email = new ParsedEmail(
            messageId: null,
            fromEmail: 'a@b.com',
            fromName: null,
            subject: 'S',
            textBody: null,
            htmlBody: '<p>Only <strong>HTML</strong></p>',
            inReplyTo: null,
            references: [],
            headers: [],
            attachments: [],
            uid: null,
        );

        $body = $email->getCleanBody();
        $this->assertStringContainsString('Only', $body);
        $this->assertStringContainsString('HTML', $body);
    }

    public function test_get_clean_body_returns_empty_when_both_bodies_empty(): void
    {
        $email = new ParsedEmail(
            messageId: null,
            fromEmail: 'a@b.com',
            fromName: null,
            subject: 'S',
            textBody: null,
            htmlBody: null,
            inReplyTo: null,
            references: [],
            headers: [],
            attachments: [],
            uid: null,
        );

        $this->assertSame('', $email->getCleanBody());
    }
}
