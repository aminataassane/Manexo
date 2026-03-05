<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrganizationFactory extends Factory
{
    protected $model = Organization::class;

    public function definition(): array
    {
        $name = fake()->company();

        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(4),
            'created_by' => User::factory(),
            'status' => 'active',
            'settings' => [],
        ];
    }

    public function suspended(): static
    {
        return $this->state(fn () => [
            'status' => 'suspended',
            'suspended_at' => now(),
            'suspension_reason' => 'Test suspension',
        ]);
    }

    public function disabled(): static
    {
        return $this->state(fn () => [
            'status' => 'disabled',
        ]);
    }
}
