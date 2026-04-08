<?php

namespace Tests\Feature\Livewire;

use App\Livewire\NotificationsBell;
use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class NotificationsBellComponentTest extends TestCase
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

        Livewire::test(NotificationsBell::class)
            ->assertOk()
            ->assertViewIs('livewire.notifications-bell');
    }

    public function test_load_notifications_marks_list_as_loaded(): void
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

        Livewire::test(NotificationsBell::class)
            ->assertSet('notificationsLoaded', false)
            ->call('loadNotifications')
            ->assertSet('notificationsLoaded', true);
    }
}
