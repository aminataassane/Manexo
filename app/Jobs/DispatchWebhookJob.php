<?php

namespace App\Jobs;

use App\Models\WebhookDelivery;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DispatchWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public int $tries = 5;

    public array $backoff = [60, 300, 900, 3600];

    public function __construct(
        public int $deliveryId,
    ) {}

    public function handle(): void
    {
        $delivery = WebhookDelivery::find($this->deliveryId);
        if (! $delivery) {
            return;
        }

        $endpoint = $delivery->endpoint;
        if (! $endpoint || ! $endpoint->is_active) {
            $delivery->update(['status' => 'failed', 'error_message' => 'Endpoint inactive or deleted.']);

            return;
        }

        $jsonPayload = json_encode($delivery->payload);
        $signature = hash_hmac('sha256', $jsonPayload, $endpoint->secret);
        $timestamp = now()->toIso8601String();

        $delivery->increment('attempt');

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'X-Manexo-Signature' => $signature,
                    'X-Manexo-Event' => $delivery->event_type,
                    'X-Manexo-Event-Id' => $delivery->event_id,
                    'X-Manexo-Timestamp' => $timestamp,
                    'User-Agent' => 'Manexo-Webhook/1.0',
                ])
                ->withBody($jsonPayload, 'application/json')
                ->post($endpoint->url);

            $statusCode = $response->status();
            $responseBody = mb_substr($response->body(), 0, 2000);

            if ($response->successful()) {
                $delivery->update([
                    'status' => 'success',
                    'http_status_code' => $statusCode,
                    'response_body' => $responseBody,
                    'delivered_at' => now(),
                ]);
            } else {
                $this->handleFailure($delivery, $statusCode, $responseBody, "HTTP {$statusCode}");
            }
        } catch (\Throwable $e) {
            $this->handleFailure($delivery, null, null, mb_substr($e->getMessage(), 0, 1000));
        }
    }

    private function handleFailure(WebhookDelivery $delivery, ?int $statusCode, ?string $responseBody, string $errorMessage): void
    {
        $attempt = $delivery->attempt;

        if ($attempt >= $this->tries) {
            $delivery->update([
                'status' => 'failed',
                'http_status_code' => $statusCode,
                'response_body' => $responseBody,
                'error_message' => $errorMessage,
            ]);

            return;
        }

        $backoffSeconds = $this->backoff[$attempt - 1] ?? 3600;
        $delivery->update([
            'status' => 'pending',
            'http_status_code' => $statusCode,
            'response_body' => $responseBody,
            'error_message' => $errorMessage,
            'next_retry_at' => now()->addSeconds($backoffSeconds),
        ]);

        $this->release($backoffSeconds);
    }

    public function failed(\Throwable $exception): void
    {
        $delivery = WebhookDelivery::find($this->deliveryId);
        if ($delivery) {
            $delivery->update([
                'status' => 'failed',
                'error_message' => mb_substr($exception->getMessage(), 0, 1000),
            ]);
        }

        Log::error('Webhook delivery permanently failed', [
            'delivery_id' => $this->deliveryId,
            'error' => $exception->getMessage(),
        ]);
    }
}
