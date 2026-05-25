<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Initiative;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Comment>
 */
class CommentFactory extends Factory
{
    protected $model = Comment::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'initiative_id' => Initiative::factory(),
            'user_id' => User::factory(),
            'body' => fake()->paragraph(),
        ];
    }
}
