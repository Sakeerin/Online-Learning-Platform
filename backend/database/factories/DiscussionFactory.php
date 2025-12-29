<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Discussion;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Discussion>
 */
class DiscussionFactory extends Factory
{
    protected $model = Discussion::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'lesson_id' => $this->faker->optional(0.4)->randomElement([
                null,
                fn () => Lesson::factory()->create()->id,
            ]),
            'user_id' => User::factory(),
            'question' => $this->faker->sentence(10, true) . '?',
            'upvotes' => $this->faker->numberBetween(0, 20),
            'is_answered' => $this->faker->boolean(30),
        ];
    }

    /**
     * Indicate that the discussion is answered.
     */
    public function answered(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_answered' => true,
        ]);
    }

    /**
     * Indicate that the discussion is unanswered.
     */
    public function unanswered(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_answered' => false,
        ]);
    }

    /**
     * Indicate that the discussion is for a specific lesson.
     */
    public function forLesson(Lesson $lesson): static
    {
        return $this->state(fn (array $attributes) => [
            'lesson_id' => $lesson->id,
            'course_id' => $lesson->section->course_id,
        ]);
    }
}

