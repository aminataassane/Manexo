<?php

namespace Tests\Unit\Services;

use App\Services\DuplicateDetectionService;
use PHPUnit\Framework\TestCase;

class DuplicateDetectionServiceTest extends TestCase
{
    public function test_find_duplicates_returns_empty_for_short_subject(): void
    {
        $result = DuplicateDetectionService::findDuplicates('abc', 1);

        $this->assertCount(0, $result);
    }
}
