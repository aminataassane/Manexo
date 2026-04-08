<?php

namespace Tests\Unit\Enums;

use App\Enums\ApiTokenScope;
use App\Enums\WebhookEvent;
use PHPUnit\Framework\TestCase;

class ApiTokenScopeAndWebhookLabelsTest extends TestCase
{
    public function test_api_token_scope_labels_cover_all_cases(): void
    {
        $labels = ApiTokenScope::labels();

        foreach (ApiTokenScope::cases() as $case) {
            $this->assertArrayHasKey($case->value, $labels);
            $this->assertNotSame('', trim($labels[$case->value]));
        }

        $this->assertCount(count(ApiTokenScope::cases()), $labels);
    }

    public function test_webhook_event_labels_cover_all_cases(): void
    {
        $labels = WebhookEvent::labels();

        foreach (WebhookEvent::cases() as $case) {
            $this->assertArrayHasKey($case->value, $labels);
            $this->assertNotSame('', trim($labels[$case->value]));
        }

        $this->assertCount(count(WebhookEvent::cases()), $labels);
    }
}
