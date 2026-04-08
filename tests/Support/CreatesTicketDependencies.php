<?php

namespace Tests\Support;

use App\Models\TicketCategory;
use App\Models\TicketPriority;

trait CreatesTicketDependencies
{
    /**
     * @return array{0: int, 1: int} [ticket_category_id, ticket_priority_id]
     */
    protected function createTicketCategoryAndPriority(int $organizationId): array
    {
        $category = TicketCategory::query()->create([
            'organization_id' => $organizationId,
            'name' => 'Test category',
            'slug' => 'tc-'.uniqid(),
            'is_active' => true,
        ]);

        $priority = TicketPriority::query()->create([
            'organization_id' => $organizationId,
            'name' => 'Normal',
            'level' => 1,
            'is_active' => true,
        ]);

        return [$category->id, $priority->id];
    }
}
