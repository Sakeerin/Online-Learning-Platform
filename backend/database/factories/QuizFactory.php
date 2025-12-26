<?php

namespace Database\Factories;

use App\Models\Lesson;
use App\Models\Quiz;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quiz>
 */
class QuizFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Quiz::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'lesson_id' => Lesson::factory(),
            'title' => fake()->sentence(3),
            'passing_score' => fake()->numberBetween(60, 100),
            'allow_retake' => true,
        ];
    }

    /**
     * Indicate that the quiz does not allow retakes.
     */
    public function noRetake(): static
    {
        return $this->state(fn (array $attributes) => [
            'allow_retake' => false,
        ]);
    }

    /**
     * Indicate that the quiz has a specific passing score.
     */
    public function withPassingScore(int $score): static
    {
        return $this->state(fn (array $attributes) => [
            'passing_score' => $score,
        ]);
    }
}

