<?php

namespace Database\Factories;

use App\Models\Lesson;
use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lesson>
 */
class LessonFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Lesson::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['video', 'quiz', 'article']);

        $content = match ($type) {
            'video' => [
                'video_url' => fake()->url(),
                'video_path' => 'videos/' . fake()->uuid() . '.mp4',
                'thumbnail' => fake()->imageUrl(640, 360),
            ],
            'quiz' => [
                'instructions' => fake()->sentence(),
            ],
            'article' => [
                'content' => fake()->paragraphs(5, true),
            ],
            default => [],
        };

        return [
            'section_id' => Section::factory(),
            'title' => fake()->sentence(4),
            'type' => $type,
            'content' => $content,
            'duration' => $type === 'video' ? fake()->numberBetween(300, 3600) : null,
            'is_preview' => false,
            'order' => 1,
        ];
    }

    /**
     * Indicate that the lesson is a video.
     */
    public function video(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'video',
            'content' => [
                'video_url' => fake()->url(),
                'video_path' => 'videos/' . fake()->uuid() . '.mp4',
                'thumbnail' => fake()->imageUrl(640, 360),
            ],
            'duration' => fake()->numberBetween(300, 3600),
        ]);
    }

    /**
     * Indicate that the lesson is a preview.
     */
    public function preview(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_preview' => true,
        ]);
    }
}

