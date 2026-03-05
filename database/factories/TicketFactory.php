<?php

namespace Database\Factories;

use App\Enums\TicketStatus;
use App\Models\Organization;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'created_by' => User::factory(),
            'status' => TicketStatus::Open,
            'subject' => fake()->sentence(),
            'description' => fake()->paragraph(),
        ];
    }

    public function closed(): static
    {
        return $this->state(fn () => [
            'status' => TicketStatus::Closed,
            'closed_at' => now(),
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn () => [
            'archived_at' => now(),
        ]);
    }
}
