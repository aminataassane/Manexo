<?php

namespace Tests\Unit\Enums;

use App\Enums\TicketSource;
use PHPUnit\Framework\TestCase;

class TicketSourceEnumTest extends TestCase
{
    public function test_all_sources_are_defined(): void
    {
        $values = array_map(fn (TicketSource $s) => $s->value, TicketSource::cases());

        $this->assertEqualsCanonicalizing(
            ['platform', 'form', 'email', 'api'],
            $values
        );
    }
}
