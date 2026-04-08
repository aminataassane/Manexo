<?php

namespace Tests\Unit\Domain;

use App\Models\Ticket;
use PHPUnit\Framework\TestCase;

class TicketShortReferenceTest extends TestCase
{
    public function test_short_reference_truncates_ulid_part(): void
    {
        $ticket = new Ticket([
            'public_id' => 'TCK-01J9ZK5RXYZABCDEF',
        ]);

        $this->assertSame('TCK-01J9ZK5R', $ticket->shortReference());
    }

    public function test_short_reference_falls_back_when_no_separator(): void
    {
        $ticket = new Ticket([
            'public_id' => 'INVALID',
        ]);

        $this->assertSame('INVALID', $ticket->shortReference());
    }
}
