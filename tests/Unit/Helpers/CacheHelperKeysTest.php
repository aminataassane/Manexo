<?php

namespace Tests\Unit\Helpers;

use App\Helpers\CacheHelper;
use PHPUnit\Framework\TestCase;

/**
 * Ensures cache key shapes stay stable (monitoring, invalidation, docs).
 */
class CacheHelperKeysTest extends TestCase
{
    public function test_dashboard_kpis_key_includes_org_id(): void
    {
        $this->assertSame('dashboard:kpis:42', CacheHelper::dashboardKpisKey(42));
    }

    public function test_role_permissions_key_format(): void
    {
        $this->assertSame('org_perms:10:admin', CacheHelper::rolePermissionsKey(10, 'admin'));
    }

    public function test_categories_key_respects_active_variant(): void
    {
        $this->assertSame('categories:5:active', CacheHelper::categoriesKey(5, true));
        $this->assertSame('categories:5:all', CacheHelper::categoriesKey(5, false));
    }

    public function test_forms_list_key(): void
    {
        $this->assertSame('forms_list:7', CacheHelper::formsListKey(7));
    }

    public function test_ttl_constants_are_positive(): void
    {
        $this->assertGreaterThan(0, CacheHelper::TTL);
        $this->assertGreaterThan(0, CacheHelper::TTL_SHORT);
        $this->assertGreaterThan(CacheHelper::TTL_SHORT, CacheHelper::TTL_CONFIG);
    }
}
