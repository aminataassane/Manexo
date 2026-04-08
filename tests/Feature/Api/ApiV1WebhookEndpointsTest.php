<?php

namespace Tests\Feature\Api;

use App\Enums\WebhookEvent;
use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiV1WebhookEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhooks_manage_scope_allows_listing_endpoints(): void
    {
        $user = User::factory()->create();
        $org = Organization::factory()->create(['created_by' => $user->id]);
        OrganizationMembership::query()->create([
            'organization_id' => $org->id,
            'user_id' => $user->id,
            'role' => 'owner',
        ]);

        $plain = $this->createSanctumTokenForOrganization($user, $org, ['webhooks:manage']);

        $this->withHeader('Authorization', 'Bearer '.$plain)
            ->getJson('/api/v1/webhooks')
            ->assertOk()
            ->assertJsonStructure(['data']);
    }

    public function test_read_only_token_cannot_access_webhooks(): void
    {
        $user = User::factory()->create();
        $org = Organization::factory()->create(['created_by' => $user->id]);
        OrganizationMembership::query()->create([
            'organization_id' => $org->id,
            'user_id' => $user->id,
            'role' => 'owner',
        ]);

        $plain = $this->createSanctumTokenForOrganization($user, $org, ['tickets:read']);

        $this->withHeader('Authorization', 'Bearer '.$plain)
            ->getJson('/api/v1/webhooks')
            ->assertStatus(403);
    }

    public function test_webhooks_manage_can_create_endpoint(): void
    {
        $user = User::factory()->create();
        $org = Organization::factory()->create(['created_by' => $user->id]);
        OrganizationMembership::query()->create([
            'organization_id' => $org->id,
            'user_id' => $user->id,
            'role' => 'owner',
        ]);

        $plain = $this->createSanctumTokenForOrganization($user, $org, ['webhooks:manage']);

        $response = $this->withHeader('Authorization', 'Bearer '.$plain)
            ->postJson('/api/v1/webhooks', [
                'url' => 'https://example.com/hooks/manexo',
                'events' => [WebhookEvent::TicketCreated->value],
            ]);

        $response->assertCreated();
        $this->assertDatabaseHas('webhook_endpoints', [
            'organization_id' => $org->id,
            'url' => 'https://example.com/hooks/manexo',
        ]);
    }
}
