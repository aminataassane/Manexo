<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Dashboard\OnboardingChecklist;
use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OnboardingChecklistComponentTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_for_authenticated_user_with_organization_session(): void
    {
        $user = User::factory()->create();
        $org = Organization::factory()->create(['created_by' => $user->id]);
        OrganizationMembership::query()->create([
            'organization_id' => $org->id,
            'user_id' => $user->id,
            'role' => 'owner',
        ]);

        $this->actingAs($user);
        session(['current_organization_id' => $org->id]);

        Livewire::test(OnboardingChecklist::class)
            ->assertOk()
            ->assertViewIs('livewire.dashboard.onboarding-checklist');
    }

    public function test_total_steps_matches_onboarding_service(): void
    {
        $user = User::factory()->create();
        $org = Organization::factory()->create(['created_by' => $user->id]);
        OrganizationMembership::query()->create([
            'organization_id' => $org->id,
            'user_id' => $user->id,
            'role' => 'owner',
        ]);

        $this->actingAs($user);
        session(['current_organization_id' => $org->id]);

        Livewire::test(OnboardingChecklist::class)
            ->assertSet('totalSteps', 7);
    }
}
