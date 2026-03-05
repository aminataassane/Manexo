<?php

namespace Database\Factories;

use App\Enums\FormStatus;
use App\Models\Form;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FormFactory extends Factory
{
    protected $model = Form::class;

    public function definition(): array
    {
        $name = fake()->words(3, true);

        return [
            'organization_id' => Organization::factory(),
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(4),
            'status' => FormStatus::Draft,
            'is_public' => false,
            'creates_ticket' => true,
            'current_version' => 1,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'status' => FormStatus::Published,
        ]);
    }

    public function publicForm(): static
    {
        return $this->state(fn () => [
            'is_public' => true,
            'status' => FormStatus::Published,
        ]);
    }
}
