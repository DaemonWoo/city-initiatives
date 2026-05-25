<?php

namespace Database\Factories;

use App\Models\Initiative;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Initiative>
 */
class InitiativeFactory extends Factory
{
    protected $model = Initiative::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'image' => null,
        ];
    }

    public function withImage(string $path = 'initiatives/test.jpg'): static
    {
        return $this->state(fn (array $attributes) => [
            'image' => $path,
        ]);
    }
}
