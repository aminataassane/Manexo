<?php

namespace Tests\Feature\Services;

use App\Enums\WebhookEvent;
use App\Jobs\DispatchWebhookJob;
use App\Models\Organization;
use App\Models\User;
use App\Models\WebhookDelivery;
use App\Models\WebhookEndpoint;
use App\Services\WebhookService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class WebhookServiceDispatchTest extends TestCase
{
    use RefreshDatabase;

    public function test_dispatches_job_when_endpoint_subscribes_to_event(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $org = Organization::factory()->create(['created_by' => $user->id]);

        WebhookEndpoint::query()->create([
            'organization_id' => $org->id,
            'url' => 'https://example.com/hook',
            'secret' => str_repeat('a', 64),
            'events' => [WebhookEvent::TicketCreated->value],
            'is_active' => true,
            'created_by' => $user->id,
        ]);

        WebhookService::dispatch((int) $org->id, 'ticket.created', ['ticket_id' => 'pub_01']);

        $delivery = WebhookDelivery::query()->first();
        $this->assertNotNull($delivery);
        $this->assertSame('pending', $delivery->status);
        $this->assertSame('ticket.created', $delivery->event_type);

        Queue::assertPushed(DispatchWebhookJob::class, function (DispatchWebhookJob $job) use ($delivery) {
            return $job->deliveryId === $delivery->id;
        });
        Queue::assertPushed(DispatchWebhookJob::class, 1);
    }

    public function test_does_not_queue_when_no_endpoint_subscribes_to_event(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $org = Organization::factory()->create(['created_by' => $user->id]);

        WebhookEndpoint::query()->create([
            'organization_id' => $org->id,
            'url' => 'https://example.com/other',
            'secret' => str_repeat('b', 64),
            'events' => [WebhookEvent::TicketStatusChanged->value],
            'is_active' => true,
            'created_by' => $user->id,
        ]);

        WebhookService::dispatch((int) $org->id, 'ticket.created', []);

        Queue::assertNothingPushed();
        $this->assertSame(0, WebhookDelivery::query()->count());
    }

    public function test_dispatches_one_job_per_subscribed_endpoint(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $org = Organization::factory()->create(['created_by' => $user->id]);
        $base = [
            'organization_id' => $org->id,
            'secret' => str_repeat('c', 64),
            'events' => [WebhookEvent::TicketCreated->value],
            'is_active' => true,
            'created_by' => $user->id,
        ];

        WebhookEndpoint::query()->create(array_merge($base, ['url' => 'https://a.example.com/h']));
        WebhookEndpoint::query()->create(array_merge($base, ['url' => 'https://b.example.com/h']));

        WebhookService::dispatch((int) $org->id, 'ticket.created', ['ticket_id' => 'x']);

        $this->assertSame(2, WebhookDelivery::query()->count());
        Queue::assertPushed(DispatchWebhookJob::class, 2);
    }
}
