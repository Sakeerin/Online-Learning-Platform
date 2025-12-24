<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    protected $model = Review::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'course_id' => Course::factory(),
            'rating' => $this->faker->numberBetween(1, 5),
            'review_text' => $this->faker->optional(0.8)->paragraph(),
            'helpful_votes' => $this->faker->numberBetween(0, 50),
            'instructor_response' => $this->faker->optional(0.3)->paragraph(),
            'responded_at' => $this->faker->optional(0.3)->dateTimeBetween('-1 year', 'now'),
            'is_flagged' => false,
        ];
    }

    /**
     * Indicate that the review is flagged.
     */
    public function flagged(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_flagged' => true,
        ]);
    }

    /**
     * Indicate that the review has an instructor response.
     */
    public function withResponse(): static
    {
        return $this->state(fn (array $attributes) => [
            'instructor_response' => $this->faker->paragraph(),
            'responded_at' => now(),
        ]);
    }
}

