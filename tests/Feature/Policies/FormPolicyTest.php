<?php

namespace Tests\Feature\Policies;

use App\Helpers\PermissionSeeder;
use App\Models\Form;
use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class FormPolicyTest extends TestCase
{
    use RefreshDatabase;

    private function seedOrgPermissions(int $organizationId): void
    {
        Cache::flush();
        PermissionSeeder::seedForOrganization($organizationId);
    }

    public function test_admin_can_create_form(): void
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

        $this->assertTrue(Gate::forUser($user)->allows('create', Form::class));
    }

    public function test_member_cannot_create_form_without_forms_manage(): void
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

        $this->assertFalse(Gate::forUser($user)->allows('create', Form::class));
    }

    public function test_agent_can_view_responses_when_permission_present(): void
    {
        $user = User::factory()->create();
        $org = Organization::factory()->create(['created_by' => $user->id]);
        $this->seedOrgPermissions($org->id);

        OrganizationMembership::query()->create([
            'organization_id' => $org->id,
            'user_id' => $user->id,
            'role' => 'agent',
        ]);

        $form = Form::factory()->create([
            'organization_id' => $org->id,
        ]);

        session(['current_organization_id' => $org->id]);

        $this->assertTrue(Gate::forUser($user)->allows('viewResponses', $form));
    }
}
