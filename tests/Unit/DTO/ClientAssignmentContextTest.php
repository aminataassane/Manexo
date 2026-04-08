<?php

namespace Tests\Unit\DTO;

use App\DataTransferObjects\ClientAssignmentClientScenario;
use App\DataTransferObjects\ClientAssignmentContext;
use PHPUnit\Framework\TestCase;

class ClientAssignmentContextTest extends TestCase
{
    public function test_generic_factory_uses_generic_scenario(): void
    {
        $ctx = ClientAssignmentContext::generic();

        $this->assertSame(ClientAssignmentClientScenario::Generic, $ctx->clientScenario);
        $this->assertNull($ctx->targetDisplayName);
    }
}
