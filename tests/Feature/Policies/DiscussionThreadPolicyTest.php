<?php

namespace Tests\Feature\Policies;

use App\Helpers\PermissionSeeder;
use App\Models\DiscussionThread;
use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\Scopes\OrganizationScope;
use App\Models\User;
use App\Policies\DiscussionThreadPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class DiscussionThreadPolicyTest extends TestCase
{
    use RefreshDatabase;

    private function seedOrgPermissions(int $organizationId): void
    {
        Cache::flush();
        PermissionSeeder::seedForOrganization($organizationId);
    }

    public function test_creator_can_view_thread(): void
    {
        $creator = User::factory()->create();
        $org = Organization::factory()->create(['created_by' => $creator->id]);
        $this->seedOrgPermissions($org->id);

        OrganizationMembership::query()->create([
            'organization_id' => $org->id,
            'user_id' => $creator->id,
            'role' => 'member',
        ]);

        $thread = DiscussionThread::withoutGlobalScope(OrganizationScope::class)->create([
            'organization_id' => $org->id,
            'created_by' => $creator->id,
            'name' => 'Thread A',
            'is_group' => false,
        ]);

        session(['current_organization_id' => $org->id]);

        $this->assertTrue(Gate::forUser($creator)->allows('view', $thread));
    }

    public function test_participant_can_view_without_being_creator(): void
    {
        $creator = User::factory()->create();
        $participant = User::factory()->create();
        $org = Organization::factory()->create(['created_by' => $creator->id]);
        $this->seedOrgPermissions($org->id);

        foreach ([$creator, $participant] as $u) {
            OrganizationMembership::query()->create([
                'organization_id' => $org->id,
                'user_id' => $u->id,
                'role' => 'member',
            ]);
        }

        $thread = DiscussionThread::withoutGlobalScope(OrganizationScope::class)->create([
            'organization_id' => $org->id,
            'created_by' => $creator->id,
            'name' => 'Thread B',
            'is_group' => false,
        ]);

        $thread->participants()->attach($participant->id, ['added_by' => $creator->id]);

        session(['current_organization_id' => $org->id]);

        $this->assertTrue(Gate::forUser($participant)->allows('view', $thread));
    }

    public function test_non_participant_non_creator_cannot_view(): void
    {
        $creator = User::factory()->create();
        $outsider = User::factory()->create();
        $org = Organization::factory()->create(['created_by' => $creator->id]);
        $this->seedOrgPermissions($org->id);

        foreach ([$creator, $outsider] as $u) {
            OrganizationMembership::query()->create([
                'organization_id' => $org->id,
                'user_id' => $u->id,
                'role' => 'member',
            ]);
        }

        $thread = DiscussionThread::withoutGlobalScope(OrganizationScope::class)->create([
            'organization_id' => $org->id,
            'created_by' => $creator->id,
            'name' => 'Thread C',
            'is_group' => false,
        ]);

        session(['current_organization_id' => $org->id]);

        $this->assertFalse(Gate::forUser($outsider)->allows('view', $thread));
    }

    public function test_creator_can_add_participant(): void
    {
        $creator = User::factory()->create();
        $org = Organization::factory()->create(['created_by' => $creator->id]);
        $this->seedOrgPermissions($org->id);

        OrganizationMembership::query()->create([
            'organization_id' => $org->id,
            'user_id' => $creator->id,
            'role' => 'member',
        ]);

        $thread = DiscussionThread::withoutGlobalScope(OrganizationScope::class)->create([
            'organization_id' => $org->id,
            'created_by' => $creator->id,
            'name' => 'Thread D',
            'is_group' => false,
        ]);

        session(['current_organization_id' => $org->id]);

        $this->assertTrue(Gate::forUser($creator)->allows('addParticipant', $thread));
    }

    public function test_agent_has_view_internal_notes_permission(): void
    {
        $agent = User::factory()->create();
        $org = Organization::factory()->create(['created_by' => $agent->id]);
        $this->seedOrgPermissions($org->id);

        OrganizationMembership::query()->create([
            'organization_id' => $org->id,
            'user_id' => $agent->id,
            'role' => 'agent',
        ]);

        session(['current_organization_id' => $org->id]);

        $policy = new DiscussionThreadPolicy;

        $this->assertTrue($policy->viewInternalNotes($agent));
    }
}
