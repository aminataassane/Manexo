<?php

namespace Tests\Unit\Domain;

use App\Models\Ticket;
use PHPUnit\Framework\TestCase;

class HasPublicIdGeneratePublicIdTest extends TestCase
{
    public function test_generate_public_id_matches_prefix_and_ulid_shape(): void
    {
        $id = Ticket::generatePublicId();

        $this->assertMatchesRegularExpression('/^TCK-[0-9A-Z]{26}$/', $id);
    }

    public function test_generate_public_id_is_unique_across_calls(): void
    {
        $a = Ticket::generatePublicId();
        $b = Ticket::generatePublicId();

        $this->assertNotSame($a, $b);
    }
}
