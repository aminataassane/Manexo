<?php

namespace Tests\Unit\Services;

use App\Models\Organization;
use App\Services\OnboardingService;
use PHPUnit\Framework\TestCase;

class OnboardingServiceConstantsTest extends TestCase
{
    public function test_steps_order_is_stable_and_documented_length(): void
    {
        $this->assertSame(
            ['categories', 'priorities', 'functions', 'invite_team', 'customize', 'create_form', 'first_test'],
            OnboardingService::STEPS
        );
    }

    public function test_is_dismissed_by_org_reads_settings_flag(): void
    {
        $org = new Organization([
            'settings' => ['onboarding_dismissed' => true],
        ]);

        $this->assertTrue(OnboardingService::isDismissedByOrg($org));

        $org2 = new Organization(['settings' => []]);
        $this->assertFalse(OnboardingService::isDismissedByOrg($org2));
    }
}
