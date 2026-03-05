<?php

namespace Tests\Feature\Security;

use App\Enums\TicketStatus;
use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Scopes\OrganizationScope;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private function createOrgWithUser(string $role = 'member'): array
    {
        $user = User::factory()->create();
        $org = Organization::factory()->create(['created_by' => $user->id]);
        OrganizationMembership::create([
            'organization_id' => $org->id,
            'user_id' => $user->id,
            'role' => $role,
        ]);

        return [$user, $org];
    }

    public function test_member_cannot_access_admin_settings(): void
    {
        [$user, $org] = $this->createOrgWithUser('member');

        $response = $this->actingAs($user)
            ->withSession(['current_organization_id' => $org->id])
            ->get('/admin/settings');

        $response->assertStatus(403);
    }

    public function test_member_cannot_access_admin_users(): void
    {
        [$user, $org] = $this->createOrgWithUser('member');

        $response = $this->actingAs($user)
            ->withSession(['current_organization_id' => $org->id])
            ->get('/admin/users');

        $response->assertStatus(403);
    }

    public function test_ticket_policy_denies_view_for_non_participant(): void
    {
        [$creator, $org] = $this->createOrgWithUser('admin');
        [$outsider, $orgB] = $this->createOrgWithUser('member');

        $ticket = Ticket::withoutGlobalScope(OrganizationScope::class)->create([
            'organization_id' => $org->id,
            'created_by' => $creator->id,
            'subject' => 'Private ticket',
            'description' => 'Test',
            'status' => TicketStatus::Open,
        ]);

        // Outsider should not be able to view ticket (different org)
        $this->assertFalse($ticket->hasDiscussionAccess($outsider->id));
    }

    public function test_ticket_creator_has_discussion_access(): void
    {
        [$creator, $org] = $this->createOrgWithUser('member');

        $ticket = Ticket::withoutGlobalScope(OrganizationScope::class)->create([
            'organization_id' => $org->id,
            'created_by' => $creator->id,
            'subject' => 'My ticket',
            'description' => 'Test',
            'status' => TicketStatus::Open,
        ]);

        $this->assertTrue($ticket->hasDiscussionAccess($creator->id));
    }

    public function test_org_member_has_discussion_access(): void
    {
        [$creator, $org] = $this->createOrgWithUser('admin');

        $member = User::factory()->create();
        OrganizationMembership::create([
            'organization_id' => $org->id,
            'user_id' => $member->id,
            'role' => 'member',
        ]);

        $ticket = Ticket::withoutGlobalScope(OrganizationScope::class)->create([
            'organization_id' => $org->id,
            'created_by' => $creator->id,
            'subject' => 'Team ticket',
            'description' => 'Test',
            'status' => TicketStatus::Open,
        ]);

        $this->assertTrue($ticket->hasDiscussionAccess($member->id));
    }

    public function test_non_platform_admin_cannot_access_super_admin_dashboard(): void
    {
        [$user, $org] = $this->createOrgWithUser('admin');

        $response = $this->actingAs($user)
            ->get('/platform-admin/dashboard');

        $response->assertRedirect(route('platform-admin.login'));
    }
}
