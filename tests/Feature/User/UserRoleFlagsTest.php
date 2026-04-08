<?php

namespace Tests\Feature\User;

use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRoleFlagsTest extends TestCase
{
    use RefreshDatabase;

    private function attach(User $user, Organization $org, string $role): void
    {
        OrganizationMembership::query()->create([
            'organization_id' => $org->id,
            'user_id' => $user->id,
            'role' => $role,
        ]);
    }

    public function test_guest_is_never_assignable_even_with_member_pivot(): void
    {
        $user = User::factory()->create();
        $user->forceFill(['status' => 'guest'])->saveQuietly();

        $org = Organization::factory()->create(['created_by' => $user->id]);
        $this->attach($user, $org, 'member');

        $this->assertFalse($user->isTicketAssignableMember($org->id));
        $this->assertFalse($user->isInternalStaff($org->id));
    }

    public function test_agent_is_internal_staff_and_assignable(): void
    {
        $user = User::factory()->create();
        $org = Organization::factory()->create(['created_by' => $user->id]);
        $this->attach($user, $org, 'agent');

        $this->assertTrue($user->isInternalStaff($org->id));
        $this->assertTrue($user->isTicketAssignableMember($org->id));
        $this->assertFalse($user->isExternalParticipant($org->id));
    }

    public function test_member_is_not_internal_staff_but_is_assignable(): void
    {
        $user = User::factory()->create();
        $org = Organization::factory()->create(['created_by' => $user->id]);
        $this->attach($user, $org, 'member');

        $this->assertFalse($user->isInternalStaff($org->id));
        $this->assertTrue($user->isTicketAssignableMember($org->id));
        $this->assertTrue($user->isExternalParticipant($org->id));
    }

    public function test_user_without_membership_has_no_role_flags(): void
    {
        $user = User::factory()->create();
        $org = Organization::factory()->create();

        $this->assertFalse($user->isInternalStaff($org->id));
        $this->assertFalse($user->isTicketAssignableMember($org->id));
    }
}
