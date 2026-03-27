<?php

namespace App\Services;

use App\Jobs\DispatchWebhookJob;
use App\Models\WebhookDelivery;
use App\Models\WebhookEndpoint;
use Illuminate\Support\Str;

class WebhookService
{
    public static function dispatch(int $orgId, string $event, array $data): void
    {
        $endpoints = WebhookEndpoint::where('organization_id', $orgId)
            ->where('is_active', true)
            ->get();

        foreach ($endpoints as $endpoint) {
            if (! $endpoint->subscribesTo($event)) {
                continue;
            }

            $eventId = (string) Str::uuid();
            $delivery = WebhookDelivery::create([
                'webhook_endpoint_id' => $endpoint->id,
                'event_id' => $eventId,
                'event_type' => $event,
                'payload' => [
                    'event_id' => $eventId,
                    'event' => $event,
                    'timestamp' => now()->toIso8601String(),
                    'organization_id' => $orgId,
                    'data' => $data,
                ],
                'status' => 'pending',
            ]);

            DispatchWebhookJob::dispatch((int) $delivery->id);
        }
    }
}
