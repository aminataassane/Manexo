<?php

namespace Tests\Unit\Permissions;

use App\Enums\Permission;
use PHPUnit\Framework\TestCase;

class PermissionGroupedTest extends TestCase
{
    public function test_grouped_contains_all_permission_cases_exactly_once(): void
    {
        $allCases = Permission::cases();
        $grouped = Permission::grouped();
        $flat = [];

        foreach ($grouped as $key => $permissions) {
            $this->assertIsArray($permissions);
            $this->assertNotSame('', $key);
            foreach ($permissions as $p) {
                $flat[] = $p;
            }
        }

        $this->assertCount(count($allCases), $flat);

        foreach ($allCases as $permission) {
            $count = 0;
            foreach ($flat as $p) {
                if ($p === $permission) {
                    $count++;
                }
            }
            $this->assertSame(1, $count, $permission->value.' must appear exactly once in grouped()');
        }
    }

    public function test_grouped_has_expected_top_level_keys(): void
    {
        $keys = array_keys(Permission::grouped());

        $this->assertEqualsCanonicalizing(
            ['tickets', 'team', 'forms', 'settings', 'reports', 'discussions'],
            $keys
        );
    }
}
