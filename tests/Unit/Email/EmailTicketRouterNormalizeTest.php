<?php

namespace Tests\Unit\Email;

use App\Services\Email\EmailTicketRouter;
use PHPUnit\Framework\TestCase;

class EmailTicketRouterNormalizeTest extends TestCase
{
    public function test_normalize_message_id_strips_chevrons_and_spaces(): void
    {
        $this->assertSame('abc@example.com', EmailTicketRouter::normalizeMessageId('<abc@example.com>'));
        $this->assertSame('abc@example.com', EmailTicketRouter::normalizeMessageId('  <abc@example.com>  '));
    }

    public function test_normalize_message_id_returns_empty_for_null_or_blank(): void
    {
        $this->assertSame('', EmailTicketRouter::normalizeMessageId(null));
        $this->assertSame('', EmailTicketRouter::normalizeMessageId('   '));
    }

    public function test_normalize_message_id_strips_tabs_and_vertical_whitespace(): void
    {
        $this->assertSame('id@host', EmailTicketRouter::normalizeMessageId("\t<id@host>\r"));
    }
}
