<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Course::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = ['Development', 'Design', 'Business', 'Marketing', 'Photography', 'Music'];
        $difficultyLevels = ['beginner', 'intermediate', 'advanced'];

        return [
            'instructor_id' => User::factory(),
            'title' => fake()->sentence(4),
            'subtitle' => fake()->sentence(8),
            'description' => fake()->paragraphs(3, true),
            'learning_objectives' => [
                fake()->sentence(),
                fake()->sentence(),
                fake()->sentence(),
            ],
            'category' => fake()->randomElement($categories),
            'subcategory' => fake()->words(2, true),
            'difficulty_level' => fake()->randomElement($difficultyLevels),
            'thumbnail' => fake()->imageUrl(640, 480, 'education'),
            'price' => fake()->randomFloat(2, 0, 199.99),
            'currency' => 'THB',
            'status' => 'draft',
            'published_at' => null,
            'average_rating' => null,
            'total_enrollments' => 0,
            'total_reviews' => 0,
        ];
    }

    /**
     * Indicate that the course is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    /**
     * Indicate that the course is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'published_at' => null,
        ]);
    }

    /**
     * Indicate that the course is free.
     */
    public function free(): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => 0.00,
        ]);
    }
}

