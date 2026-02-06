<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Review;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Seed 20-30 reviews across published courses.
     * Only enrolled students can leave reviews. Ratings range 3-5.
     * Updates course average_rating and total_reviews after seeding.
     */
    public function run(): void
    {
        $enrollments = Enrollment::all();
        $reviewTexts = [
            5 => [
                'Absolutely fantastic course! The instructor explains complex topics in a clear and engaging way. Highly recommended for anyone looking to level up their skills.',
                'One of the best courses I have ever taken online. The projects are practical and the content is well-structured. Worth every penny.',
                'Incredible depth of content. I went from knowing nothing to feeling confident in the subject. The instructor is truly passionate about teaching.',
                'This course exceeded my expectations. The hands-on exercises are excellent and the community support is top-notch.',
            ],
            4 => [
                'Very solid course with great content. A few sections could use more examples, but overall an excellent learning experience.',
                'Good course with practical knowledge. The pace is well-balanced and the instructor is knowledgeable. Minor improvements could be made to the quiz sections.',
                'Really enjoyed this course. The material is relevant and up-to-date. Would love to see a follow-up advanced course.',
                'Comprehensive coverage of the topic. Some lessons felt a bit rushed, but the quality of instruction makes up for it.',
                'Great value for the price. Learned a lot of practical skills I can apply immediately at work.',
            ],
            3 => [
                'Decent course that covers the basics well. I was hoping for more advanced content in the later sections.',
                'Good introduction to the subject. The production quality could be better, but the information is accurate and useful.',
                'Average course. Some parts were great, others felt like filler. Still learned useful things though.',
            ],
        ];

        $reviewCount = 0;
        $targetReviews = 25;

        // Shuffle enrollments to distribute reviews randomly
        $shuffledEnrollments = $enrollments->shuffle();

        foreach ($shuffledEnrollments as $enrollment) {
            if ($reviewCount >= $targetReviews) {
                break;
            }

            // Skip if this user already reviewed this course
            $alreadyReviewed = Review::where('user_id', $enrollment->user_id)
                ->where('course_id', $enrollment->course_id)
                ->exists();

            if ($alreadyReviewed) {
                continue;
            }

            $rating = fake()->randomElement([3, 4, 4, 4, 5, 5, 5, 5]);
            $texts = $reviewTexts[$rating];
            $reviewText = $texts[array_rand($texts)];

            // Some reviews get instructor responses
            $hasResponse = fake()->boolean(30);

            Review::create([
                'user_id' => $enrollment->user_id,
                'course_id' => $enrollment->course_id,
                'rating' => $rating,
                'review_text' => $reviewText,
                'helpful_votes' => fake()->numberBetween(0, 25),
                'instructor_response' => $hasResponse ? 'Thank you for your thoughtful review! I appreciate the feedback and am glad you found the course valuable.' : null,
                'responded_at' => $hasResponse ? now()->subDays(rand(1, 14)) : null,
                'is_flagged' => false,
            ]);

            $reviewCount++;
        }

        // Update average_rating and total_reviews for all courses
        $courses = Course::where('status', 'published')->get();
        foreach ($courses as $course) {
            $reviews = Review::where('course_id', $course->id);
            $count = $reviews->count();

            if ($count > 0) {
                $average = round($reviews->avg('rating'), 2);
                $course->update([
                    'average_rating' => $average,
                    'total_reviews' => $count,
                ]);
            }
        }
    }
}
