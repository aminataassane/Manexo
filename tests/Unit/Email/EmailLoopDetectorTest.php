<?php

namespace Tests\Unit\Email;

use App\Services\Email\EmailLoopDetector;
use PHPUnit\Framework\TestCase;

class EmailLoopDetectorTest extends TestCase
{
    public function test_skips_when_x_manexo_ticket_id_present(): void
    {
        $reason = EmailLoopDetector::shouldSkip([
            'X-Manexo-Ticket-Id' => '123',
        ], 'human@example.com');

        $this->assertSame('own_email', $reason);
    }

    public function test_skips_auto_submitted_header(): void
    {
        $reason = EmailLoopDetector::shouldSkip([
            'Auto-Submitted' => 'auto-generated',
        ], 'human@example.com');

        $this->assertSame('auto_submitted', $reason);
    }

    public function test_does_not_skip_when_auto_submitted_is_no(): void
    {
        $reason = EmailLoopDetector::shouldSkip([
            'Auto-Submitted' => 'no',
        ], 'human@example.com');

        $this->assertNull($reason);
    }

    public function test_skips_noreply_from_address(): void
    {
        $reason = EmailLoopDetector::shouldSkip([], 'noreply@company.com');

        $this->assertSame('noreply_address', $reason);
    }

    public function test_skips_when_list_unsubscribe_present(): void
    {
        $reason = EmailLoopDetector::shouldSkip([
            'List-Unsubscribe' => '<mailto:unsub@example.com>',
        ], 'reader@example.com');

        $this->assertNotNull($reason);
        $this->assertStringStartsWith('marketing_header_', (string) $reason);
    }

    public function test_skips_bulk_x_mailer_sendgrid(): void
    {
        $reason = EmailLoopDetector::shouldSkip([
            'X-Mailer' => 'SendGrid Mailer',
        ], 'sender@example.com');

        $this->assertSame('bulk_mailer_sendgrid', $reason);
    }

    public function test_human_email_with_minimal_headers_not_skipped(): void
    {
        $reason = EmailLoopDetector::shouldSkip([
            'Subject' => 'Help',
        ], 'client@example.com');

        $this->assertNull($reason);
    }
}
