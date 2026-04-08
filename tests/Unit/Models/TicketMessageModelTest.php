<?php

namespace Tests\Unit\Models;

use App\Enums\TicketMessageType;
use App\Models\TicketMessage;
use PHPUnit\Framework\TestCase;

class TicketMessageModelTest extends TestCase
{
    public function test_is_internal_note(): void
    {
        $m = new TicketMessage(['type' => TicketMessageType::InternalNote]);

        $this->assertTrue($m->isInternalNote());
        $this->assertFalse($m->isSystem());
    }

    public function test_is_system(): void
    {
        $m = new TicketMessage(['type' => TicketMessageType::System]);

        $this->assertTrue($m->isSystem());
        $this->assertFalse($m->isInternalNote());
    }
}
