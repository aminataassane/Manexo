<?php

namespace Tests\Feature\Api;

use App\Enums\TicketSource;
use App\Enums\TicketStatus;
use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\Scopes\OrganizationScope;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiV1TicketEndpointsTest extends TestCase
{
    use RefreshDatabase;

    private function actingOrgMember(User $user, Organization $org, string $role = 'owner'): void
    {
        OrganizationMembership::query()->create([
            'organization_id' => $org->id,
            'user_id' => $user->id,
            'role' => $role,
        ]);
    }

    public function test_unauthenticated_requests_receive_401(): void
    {
        $this->getJson('/api/v1/tickets')->assertUnauthorized();
    }

    public function test_token_without_organization_returns_403(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('no-org', ['tickets:read']);

        $this->withHeader('Authorization', 'Bearer '.$token->plainTextToken)
            ->getJson('/api/v1/tickets')
            ->assertStatus(403);
    }

    public function test_tickets_read_scope_allows_listing_tickets(): void
    {
        $user = User::factory()->create();
        $org = Organization::factory()->create(['created_by' => $user->id]);
        $this->actingOrgMember($user, $org);

        [$catId, $priId] = $this->createTicketCategoryAndPriority($org->id);

        Ticket::withoutGlobalScope(OrganizationScope::class)->create([
            'organization_id' => $org->id,
            'created_by' => $user->id,
            'ticket_category_id' => $catId,
            'ticket_priority_id' => $priId,
            'subject' => 'API list',
            'description' => 'Desc',
            'status' => TicketStatus::Open,
            'source' => TicketSource::Api,
        ]);

        $plain = $this->createSanctumTokenForOrganization($user, $org, ['tickets:read']);

        $response = $this->withHeader('Authorization', 'Bearer '.$plain)
            ->getJson('/api/v1/tickets');

        $response->assertOk();
        $response->assertJsonStructure(['data', 'links', 'meta']);
    }

    public function test_tickets_read_scope_allows_show_by_public_id(): void
    {
        $user = User::factory()->create();
        $org = Organization::factory()->create(['created_by' => $user->id]);
        $this->actingOrgMember($user, $org);

        [$catId, $priId] = $this->createTicketCategoryAndPriority($org->id);

        $ticket = Ticket::withoutGlobalScope(OrganizationScope::class)->create([
            'organization_id' => $org->id,
            'created_by' => $user->id,
            'ticket_category_id' => $catId,
            'ticket_priority_id' => $priId,
            'subject' => 'Show me',
            'description' => 'Desc',
            'status' => TicketStatus::Open,
            'source' => TicketSource::Api,
        ]);

        $plain = $this->createSanctumTokenForOrganization($user, $org, ['tickets:read']);

        $this->withHeader('Authorization', 'Bearer '.$plain)
            ->getJson('/api/v1/tickets/'.$ticket->public_id)
            ->assertOk()
            ->assertJsonPath('data.subject', 'Show me');
    }

    public function test_write_scope_required_for_post_tickets(): void
    {
        $user = User::factory()->create();
        $org = Organization::factory()->create(['created_by' => $user->id]);
        $this->actingOrgMember($user, $org);
        $this->createTicketCategoryAndPriority($org->id);

        $plain = $this->createSanctumTokenForOrganization($user, $org, ['tickets:read']);

        $this->withHeader('Authorization', 'Bearer '.$plain)
            ->postJson('/api/v1/tickets', [
                'subject' => 'Nouveau',
                'description' => 'Corps',
            ])
            ->assertStatus(403);
    }

    public function test_tickets_write_scope_creates_ticket(): void
    {
        $user = User::factory()->create();
        $org = Organization::factory()->create(['created_by' => $user->id]);
        $this->actingOrgMember($user, $org);
        $this->createTicketCategoryAndPriority($org->id);

        $plain = $this->createSanctumTokenForOrganization($user, $org, ['tickets:write']);

        $response = $this->withHeader('Authorization', 'Bearer '.$plain)
            ->postJson('/api/v1/tickets', [
                'subject' => 'Créé via API',
                'description' => 'Description test',
            ]);

        $response->assertCreated();
        $this->assertDatabaseHas('tickets', [
            'organization_id' => $org->id,
            'subject' => 'Créé via API',
        ]);
    }
}
