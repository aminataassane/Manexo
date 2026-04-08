<?php

namespace Tests\Feature\Policies;

use App\Helpers\PermissionSeeder;
use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class OrganizationPolicyTest extends TestCase
{
    use RefreshDatabase;

    private function seedOrgPermissions(int $organizationId): void
    {
        Cache::flush();
        PermissionSeeder::seedForOrganization($organizationId);
    }

    public function test_member_cannot_manage_settings(): void
    {
        $user = User::factory()->create();
        $org = Organization::factory()->create(['created_by' => $user->id]);
        $this->seedOrgPermissions($org->id);

        OrganizationMembership::query()->create([
            'organization_id' => $org->id,
            'user_id' => $user->id,
            'role' => 'member',
        ]);

        session(['current_organization_id' => $org->id]);

        $this->assertFalse(Gate::forUser($user)->allows('manageSettings', $org));
    }

    public function test_admin_can_manage_settings(): void
    {
        $user = User::factory()->create();
        $org = Organization::factory()->create(['created_by' => $user->id]);
        $this->seedOrgPermissions($org->id);

        OrganizationMembership::query()->create([
            'organization_id' => $org->id,
            'user_id' => $user->id,
            'role' => 'admin',
        ]);

        session(['current_organization_id' => $org->id]);

        $this->assertTrue(Gate::forUser($user)->allows('manageSettings', $org));
    }

    public function test_owner_can_delete_organization(): void
    {
        $user = User::factory()->create();
        $org = Organization::factory()->create(['created_by' => $user->id]);
        $this->seedOrgPermissions($org->id);

        OrganizationMembership::query()->create([
            'organization_id' => $org->id,
            'user_id' => $user->id,
            'role' => 'owner',
        ]);

        session(['current_organization_id' => $org->id]);

        $this->assertTrue(Gate::forUser($user)->allows('deleteOrganization', $org));
    }

    public function test_admin_cannot_delete_organization_without_permission(): void
    {
        $user = User::factory()->create();
        $org = Organization::factory()->create(['created_by' => $user->id]);
        $this->seedOrgPermissions($org->id);

        OrganizationMembership::query()->create([
            'organization_id' => $org->id,
            'user_id' => $user->id,
            'role' => 'admin',
        ]);

        session(['current_organization_id' => $org->id]);

        $this->assertFalse(Gate::forUser($user)->allows('deleteOrganization', $org));
    }

    public function test_admin_can_manage_team(): void
    {
        $user = User::factory()->create();
        $org = Organization::factory()->create(['created_by' => $user->id]);
        $this->seedOrgPermissions($org->id);

        OrganizationMembership::query()->create([
            'organization_id' => $org->id,
            'user_id' => $user->id,
            'role' => 'admin',
        ]);

        session(['current_organization_id' => $org->id]);

        $this->assertTrue(Gate::forUser($user)->allows('manageTeam', $org));
    }
}
