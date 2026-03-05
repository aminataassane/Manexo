<?php

namespace Tests\Feature\Security;

use App\Models\DiscussionThread;
use App\Models\Form;
use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Scopes\OrganizationScope;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
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

    public function test_user_cannot_see_tickets_from_other_organization(): void
    {
        [$userA, $orgA] = $this->createOrgWithUser();
        [$userB, $orgB] = $this->createOrgWithUser();

        $ticketA = Ticket::withoutGlobalScope(OrganizationScope::class)->create([
            'organization_id' => $orgA->id,
            'created_by' => $userA->id,
            'subject' => 'Org A Ticket',
            'description' => 'Test',
        ]);

        $ticketB = Ticket::withoutGlobalScope(OrganizationScope::class)->create([
            'organization_id' => $orgB->id,
            'created_by' => $userB->id,
            'subject' => 'Org B Ticket',
            'description' => 'Test',
        ]);

        // Simulate user A's session
        session(['current_organization_id' => $orgA->id]);

        $visibleTickets = Ticket::all();
        $this->assertCount(1, $visibleTickets);
        $this->assertEquals($orgA->id, $visibleTickets->first()->organization_id);
    }

    public function test_user_cannot_see_forms_from_other_organization(): void
    {
        [$userA, $orgA] = $this->createOrgWithUser();
        [$userB, $orgB] = $this->createOrgWithUser();

        Form::withoutGlobalScope(OrganizationScope::class)->create([
            'organization_id' => $orgA->id,
            'name' => 'Form A',
            'slug' => 'form-a',
        ]);

        Form::withoutGlobalScope(OrganizationScope::class)->create([
            'organization_id' => $orgB->id,
            'name' => 'Form B',
            'slug' => 'form-b',
        ]);

        session(['current_organization_id' => $orgA->id]);

        $visibleForms = Form::all();
        $this->assertCount(1, $visibleForms);
        $this->assertEquals('Form A', $visibleForms->first()->name);
    }

    public function test_global_scope_not_applied_without_session(): void
    {
        [$userA, $orgA] = $this->createOrgWithUser();
        [$userB, $orgB] = $this->createOrgWithUser();

        Ticket::withoutGlobalScope(OrganizationScope::class)->create([
            'organization_id' => $orgA->id,
            'created_by' => $userA->id,
            'subject' => 'Ticket A',
            'description' => 'Test',
        ]);

        Ticket::withoutGlobalScope(OrganizationScope::class)->create([
            'organization_id' => $orgB->id,
            'created_by' => $userB->id,
            'subject' => 'Ticket B',
            'description' => 'Test',
        ]);

        // No session set — scope should not filter
        session()->forget('current_organization_id');

        $allTickets = Ticket::all();
        $this->assertCount(2, $allTickets);
    }

    public function test_discussion_threads_are_scoped_by_organization(): void
    {
        [$userA, $orgA] = $this->createOrgWithUser();
        [$userB, $orgB] = $this->createOrgWithUser();

        DiscussionThread::withoutGlobalScope(OrganizationScope::class)->create([
            'organization_id' => $orgA->id,
            'created_by' => $userA->id,
            'name' => 'Thread A',
        ]);

        DiscussionThread::withoutGlobalScope(OrganizationScope::class)->create([
            'organization_id' => $orgB->id,
            'created_by' => $userB->id,
            'name' => 'Thread B',
        ]);

        session(['current_organization_id' => $orgA->id]);

        $threads = DiscussionThread::all();
        $this->assertCount(1, $threads);
        $this->assertEquals('Thread A', $threads->first()->name);
    }

    public function test_withoutOrganizationScope_bypasses_filter(): void
    {
        [$userA, $orgA] = $this->createOrgWithUser();
        [$userB, $orgB] = $this->createOrgWithUser();

        Ticket::withoutGlobalScope(OrganizationScope::class)->create([
            'organization_id' => $orgA->id,
            'created_by' => $userA->id,
            'subject' => 'Ticket A',
            'description' => 'Test',
        ]);

        Ticket::withoutGlobalScope(OrganizationScope::class)->create([
            'organization_id' => $orgB->id,
            'created_by' => $userB->id,
            'subject' => 'Ticket B',
            'description' => 'Test',
        ]);

        session(['current_organization_id' => $orgA->id]);

        // Using withoutOrganizationScope should see all tickets
        $allTickets = Ticket::withoutOrganizationScope()->get();
        $this->assertCount(2, $allTickets);
    }

    public function test_organization_id_auto_filled_on_create(): void
    {
        [$user, $org] = $this->createOrgWithUser();

        session(['current_organization_id' => $org->id]);

        $ticket = Ticket::create([
            'created_by' => $user->id,
            'subject' => 'Auto-filled org',
            'description' => 'Test',
            'status' => 'open',
        ]);

        $this->assertEquals($org->id, $ticket->organization_id);
    }
}
