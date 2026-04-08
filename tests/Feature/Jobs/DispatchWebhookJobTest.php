<?php

namespace Tests\Feature\Jobs;

use App\Enums\WebhookEvent;
use App\Jobs\DispatchWebhookJob;
use App\Models\Organization;
use App\Models\User;
use App\Models\WebhookDelivery;
use App\Models\WebhookEndpoint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class DispatchWebhookJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_marks_delivery_success_on_http_200(): void
    {
        Http::fake([
            'example.com/*' => Http::response('accepted', 200),
        ]);

        $user = User::factory()->create();
        $org = Organization::factory()->create(['created_by' => $user->id]);

        $endpoint = WebhookEndpoint::query()->create([
            'organization_id' => $org->id,
            'url' => 'https://example.com/webhook',
            'secret' => str_repeat('b', 64),
            'events' => [WebhookEvent::TicketCreated->value],
            'is_active' => true,
            'created_by' => $user->id,
        ]);

        $delivery = WebhookDelivery::query()->create([
            'webhook_endpoint_id' => $endpoint->id,
            'event_id' => (string) Str::uuid(),
            'event_type' => 'ticket.created',
            'payload' => ['ticket_id' => 1],
            'status' => 'pending',
            'attempt' => 0,
        ]);

        (new DispatchWebhookJob($delivery->id))->handle();

        $delivery->refresh();

        $this->assertSame('success', $delivery->status);
        $this->assertSame(200, $delivery->http_status_code);
        $this->assertNotNull($delivery->delivered_at);
        Http::assertSent(function ($request) use ($endpoint) {
            return $request->url() === $endpoint->url
                && $request->hasHeader('X-Manexo-Event', 'ticket.created');
        });
    }

    public function test_marks_failed_when_endpoint_inactive(): void
    {
        Http::fake();

        $user = User::factory()->create();
        $org = Organization::factory()->create(['created_by' => $user->id]);

        $endpoint = WebhookEndpoint::query()->create([
            'organization_id' => $org->id,
            'url' => 'https://example.com/off',
            'secret' => str_repeat('c', 64),
            'events' => [WebhookEvent::TicketCreated->value],
            'is_active' => false,
            'created_by' => $user->id,
        ]);

        $delivery = WebhookDelivery::query()->create([
            'webhook_endpoint_id' => $endpoint->id,
            'event_id' => (string) Str::uuid(),
            'event_type' => 'ticket.created',
            'payload' => [],
            'status' => 'pending',
            'attempt' => 0,
        ]);

        (new DispatchWebhookJob($delivery->id))->handle();

        $delivery->refresh();

        $this->assertSame('failed', $delivery->status);
        $this->assertStringContainsString('inactive', strtolower((string) $delivery->error_message));
        Http::assertNothingSent();
    }

    public function test_noop_when_delivery_missing(): void
    {
        Http::fake();

        (new DispatchWebhookJob(999_999))->handle();

        Http::assertNothingSent();
    }

    public function test_http_500_sets_pending_and_next_retry_when_under_max_attempts(): void
    {
        Http::fake([
            'example.com/*' => Http::response('server error', 500),
        ]);

        $user = User::factory()->create();
        $org = Organization::factory()->create(['created_by' => $user->id]);

        $endpoint = WebhookEndpoint::query()->create([
            'organization_id' => $org->id,
            'url' => 'https://example.com/webhook-retry',
            'secret' => str_repeat('d', 64),
            'events' => [WebhookEvent::TicketCreated->value],
            'is_active' => true,
            'created_by' => $user->id,
        ]);

        $delivery = WebhookDelivery::query()->create([
            'webhook_endpoint_id' => $endpoint->id,
            'event_id' => (string) Str::uuid(),
            'event_type' => 'ticket.created',
            'payload' => ['id' => 1],
            'status' => 'pending',
            'attempt' => 0,
        ]);

        (new DispatchWebhookJob($delivery->id))->handle();

        $delivery->refresh();

        $this->assertSame('pending', $delivery->status);
        $this->assertSame(500, $delivery->http_status_code);
        $this->assertNotNull($delivery->next_retry_at);
        $this->assertTrue($delivery->next_retry_at->isFuture());
    }
}
