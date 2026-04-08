<?php

namespace Tests\Feature\Policies;

use App\Enums\TicketSource;
use App\Enums\TicketStatus;
use App\Helpers\PermissionSeeder;
use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\Scopes\OrganizationScope;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class TicketPolicyTest extends TestCase
{
    use RefreshDatabase;

    private function seedOrgPermissions(int $organizationId): void
    {
        Cache::flush();
        PermissionSeeder::seedForOrganization($organizationId);
    }

    public function test_member_can_update_own_ticket_without_global_edit_permission(): void
    {
        $user = User::factory()->create();
        $org = Organization::factory()->create(['created_by' => $user->id]);
        $this->seedOrgPermissions($org->id);

        OrganizationMembership::query()->create([
            'organization_id' => $org->id,
            'user_id' => $user->id,
            'role' => 'member',
        ]);

        [$catId, $priId] = $this->createTicketCategoryAndPriority($org->id);

        $ticket = Ticket::withoutGlobalScope(OrganizationScope::class)->create([
            'organization_id' => $org->id,
            'created_by' => $user->id,
            'ticket_category_id' => $catId,
            'ticket_priority_id' => $priId,
            'subject' => 'Mine',
            'description' => 'D',
            'status' => TicketStatus::Open,
            'source' => TicketSource::Platform,
        ]);

        session(['current_organization_id' => $org->id]);

        $this->assertTrue(Gate::forUser($user)->allows('update', $ticket));
    }

    public function test_member_cannot_update_another_users_ticket_without_edit_permission(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $org = Organization::factory()->create(['created_by' => $owner->id]);
        $this->seedOrgPermissions($org->id);

        foreach ([$owner, $other] as $u) {
            OrganizationMembership::query()->create([
                'organization_id' => $org->id,
                'user_id' => $u->id,
                'role' => 'member',
            ]);
        }

        [$catId, $priId] = $this->createTicketCategoryAndPriority($org->id);

        $ticket = Ticket::withoutGlobalScope(OrganizationScope::class)->create([
            'organization_id' => $org->id,
            'created_by' => $owner->id,
            'ticket_category_id' => $catId,
            'ticket_priority_id' => $priId,
            'subject' => 'Owner ticket',
            'description' => 'D',
            'status' => TicketStatus::Open,
            'source' => TicketSource::Platform,
        ]);

        session(['current_organization_id' => $org->id]);

        $this->assertFalse(Gate::forUser($other)->allows('update', $ticket));
    }

    public function test_agent_can_update_ticket_they_did_not_create(): void
    {
        $creator = User::factory()->create();
        $agent = User::factory()->create();
        $org = Organization::factory()->create(['created_by' => $creator->id]);
        $this->seedOrgPermissions($org->id);

        OrganizationMembership::query()->create([
            'organization_id' => $org->id,
            'user_id' => $creator->id,
            'role' => 'member',
        ]);
        OrganizationMembership::query()->create([
            'organization_id' => $org->id,
            'user_id' => $agent->id,
            'role' => 'agent',
        ]);

        [$catId, $priId] = $this->createTicketCategoryAndPriority($org->id);

        $ticket = Ticket::withoutGlobalScope(OrganizationScope::class)->create([
            'organization_id' => $org->id,
            'created_by' => $creator->id,
            'ticket_category_id' => $catId,
            'ticket_priority_id' => $priId,
            'subject' => 'Team',
            'description' => 'D',
            'status' => TicketStatus::Open,
            'source' => TicketSource::Platform,
        ]);

        session(['current_organization_id' => $org->id]);

        $this->assertTrue(Gate::forUser($agent)->allows('update', $ticket));
    }

    public function test_view_allows_organization_member(): void
    {
        $user = User::factory()->create();
        $org = Organization::factory()->create(['created_by' => $user->id]);
        $this->seedOrgPermissions($org->id);

        OrganizationMembership::query()->create([
            'organization_id' => $org->id,
            'user_id' => $user->id,
            'role' => 'member',
        ]);

        [$catId, $priId] = $this->createTicketCategoryAndPriority($org->id);

        $ticket = Ticket::withoutGlobalScope(OrganizationScope::class)->create([
            'organization_id' => $org->id,
            'created_by' => $user->id,
            'ticket_category_id' => $catId,
            'ticket_priority_id' => $priId,
            'subject' => 'V',
            'description' => 'D',
            'status' => TicketStatus::Open,
            'source' => TicketSource::Platform,
        ]);

        session(['current_organization_id' => $org->id]);

        $this->assertTrue(Gate::forUser($user)->allows('view', $ticket));
    }
}
