<?php

namespace Tests\Feature\Security;

use App\Enums\TicketStatus;
use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Scopes\OrganizationScope;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_ticket_files(): void
    {
        $user = User::factory()->create();
        $org = Organization::factory()->create(['created_by' => $user->id]);

        $ticket = Ticket::withoutGlobalScope(OrganizationScope::class)->create([
            'organization_id' => $org->id,
            'created_by' => $user->id,
            'subject' => 'Test',
            'description' => 'Test',
            'status' => TicketStatus::Open,
        ]);

        $response = $this->get("/tickets/{$ticket->public_id}/files/test.txt");
        $response->assertRedirect(); // Redirected to login
    }

    public function test_unauthorized_user_cannot_access_ticket_files(): void
    {
        $owner = User::factory()->create();
        $org = Organization::factory()->create(['created_by' => $owner->id]);
        OrganizationMembership::create([
            'organization_id' => $org->id,
            'user_id' => $owner->id,
            'role' => 'owner',
        ]);

        $ticket = Ticket::withoutGlobalScope(OrganizationScope::class)->create([
            'organization_id' => $org->id,
            'created_by' => $owner->id,
            'subject' => 'Test',
            'description' => 'Test',
            'status' => TicketStatus::Open,
        ]);

        // Create a different user in a different org
        $outsider = User::factory()->create();
        $orgB = Organization::factory()->create(['created_by' => $outsider->id]);
        OrganizationMembership::create([
            'organization_id' => $orgB->id,
            'user_id' => $outsider->id,
            'role' => 'member',
        ]);

        // Outsider tries to access the ticket file
        $response = $this->actingAs($outsider)
            ->withSession(['current_organization_id' => $orgB->id])
            ->get("/tickets/{$ticket->public_id}/files/test.txt");

        // Should get 403 (no access) or 404 (ticket not found due to scope)
        $this->assertTrue(in_array($response->status(), [403, 404]));
    }

    public function test_files_not_accessible_via_public_url(): void
    {
        // Files should be stored on 'local' disk (not 'public')
        // Verify that the storage/app directory is not publicly accessible
        Storage::disk('local')->put('ticket-messages/test/secret.txt', 'secret content');

        // Public URL should not serve this file
        $response = $this->get('/storage/ticket-messages/test/secret.txt');
        $response->assertStatus(404);

        // Cleanup
        Storage::disk('local')->delete('ticket-messages/test/secret.txt');
    }
}
