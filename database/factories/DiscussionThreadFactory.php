<?php

namespace Database\Factories;

use App\Models\DiscussionThread;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DiscussionThreadFactory extends Factory
{
    protected $model = DiscussionThread::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'created_by' => User::factory(),
            'name' => fake()->sentence(3),
            'is_group' => true,
        ];
    }
}
