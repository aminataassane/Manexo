<?php

namespace Tests\Unit\Enums;

use App\Enums\ApiTokenScope;
use App\Enums\FormAssignmentStatus;
use App\Enums\FormStatus;
use App\Enums\OrganizationRole;
use App\Enums\Permission;
use App\Enums\PlatformRole;
use App\Enums\SlaStatus;
use App\Enums\TicketMessageType;
use App\Enums\TicketSource;
use App\Enums\TicketStatus;
use App\Enums\WebhookEvent;
use PHPUnit\Framework\TestCase;

/**
 * Canonical string values for string-backed enums (guards refactors / typos).
 */
class EnumsCatalogTest extends TestCase
{
    public function test_ticket_status_values(): void
    {
        $this->assertEqualsCanonicalizing(
            ['open', 'in_progress', 'pending', 'resolved', 'closed'],
            array_map(fn ($c) => $c->value, TicketStatus::cases())
        );
    }

    public function test_organization_role_values(): void
    {
        $this->assertEqualsCanonicalizing(
            ['owner', 'admin', 'agent', 'member'],
            array_map(fn ($c) => $c->value, OrganizationRole::cases())
        );
    }

    public function test_ticket_message_type_values(): void
    {
        $this->assertEqualsCanonicalizing(
            ['message', 'system', 'internal_note'],
            array_map(fn ($c) => $c->value, TicketMessageType::cases())
        );
    }

    public function test_form_status_values(): void
    {
        $this->assertEqualsCanonicalizing(
            ['draft', 'published', 'archived'],
            array_map(fn ($c) => $c->value, FormStatus::cases())
        );
    }

    public function test_form_assignment_status_values(): void
    {
        $this->assertEqualsCanonicalizing(
            ['pending', 'submitted', 'overdue', 'expired'],
            array_map(fn ($c) => $c->value, FormAssignmentStatus::cases())
        );
    }

    public function test_sla_status_values(): void
    {
        $this->assertEqualsCanonicalizing(
            ['none', 'on_track', 'at_risk', 'breached', 'met'],
            array_map(fn ($c) => $c->value, SlaStatus::cases())
        );
    }

    public function test_platform_role_values(): void
    {
        $this->assertEqualsCanonicalizing(
            ['super_admin', 'platform_admin', 'platform_observer'],
            array_map(fn ($c) => $c->value, PlatformRole::cases())
        );
    }

    public function test_all_string_enums_have_unique_values_within_each_enum(): void
    {
        $enums = [
            TicketStatus::class,
            OrganizationRole::class,
            PlatformRole::class,
            TicketMessageType::class,
            FormStatus::class,
            FormAssignmentStatus::class,
            SlaStatus::class,
            TicketSource::class,
            ApiTokenScope::class,
            WebhookEvent::class,
            Permission::class,
        ];

        foreach ($enums as $class) {
            $values = array_map(fn ($c) => $c->value, $class::cases());
            $this->assertSame(count($values), count(array_unique($values)), $class);
        }
    }
}
