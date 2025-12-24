<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReviewService
{
    /**
     * Create a new review for a course.
     */
    public function create(User $user, Course $course, array $data): Review
    {
        return DB::transaction(function () use ($user, $course, $data) {
            // Check if user is enrolled
            $enrollment = Enrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->first();

            if (!$enrollment) {
                throw new \Exception('You must be enrolled in this course to leave a review.');
            }

            // Check if review already exists
            $existingReview = Review::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->first();

            if ($existingReview) {
                throw new \Exception('You have already reviewed this course.');
            }

            // Create review
            $review = Review::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'rating' => $data['rating'],
                'review_text' => $data['review_text'] ?? null,
                'helpful_votes' => 0,
                'is_flagged' => false,
            ]);

            // Update course rating statistics
            $this->updateCourseRating($course);

            return $review->load(['user', 'course']);
        });
    }

    /**
     * Update an existing review.
     */
    public function update(Review $review, array $data): Review
    {
        return DB::transaction(function () use ($review, $data) {
            $review->update([
                'rating' => $data['rating'] ?? $review->rating,
                'review_text' => $data['review_text'] ?? $review->review_text,
            ]);

            // Update course rating statistics
            $this->updateCourseRating($review->course);

            return $review->fresh(['user', 'course']);
        });
    }

    /**
     * Delete a review.
     */
    public function delete(Review $review): void
    {
        DB::transaction(function () use ($review) {
            $course = $review->course;
            $review->delete();

            // Update course rating statistics
            $this->updateCourseRating($course);
        });
    }

    /**
     * Flag a review for moderation.
     */
    public function flag(Review $review): Review
    {
        $review->flag();
        return $review->fresh();
    }

    /**
     * Unflag a review (moderation action).
     */
    public function unflag(Review $review): Review
    {
        $review->unflag();
        return $review->fresh();
    }

    /**
     * Add instructor response to a review.
     */
    public function addInstructorResponse(Review $review, string $response, User $instructor): Review
    {
        // Verify instructor owns the course
        if ($review->course->instructor_id !== $instructor->id) {
            throw new \Exception('You are not authorized to respond to this review.');
        }

        return DB::transaction(function () use ($review, $response) {
            $review->update([
                'instructor_response' => $response,
                'responded_at' => now(),
            ]);

            return $review->fresh(['user', 'course']);
        });
    }

    /**
     * Get reviews for a course with pagination.
     */
    public function getCourseReviews(Course $course, array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Review::where('course_id', $course->id)
            ->where('is_flagged', false) // Only show unflagged reviews
            ->with(['user:id,name,profile_photo']);

        // Filter by rating
        if (isset($filters['rating'])) {
            $query->where('rating', $filters['rating']);
        }

        // Sort by helpful votes or date
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';

        if ($sortBy === 'helpful') {
            $query->orderBy('helpful_votes', $sortOrder);
        } else {
            $query->orderBy('created_at', $sortOrder);
        }

        $perPage = $filters['per_page'] ?? 10;

        return $query->paginate($perPage);
    }

    /**
     * Get a user's review for a course.
     */
    public function getUserReview(User $user, Course $course): ?Review
    {
        return Review::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->with(['user', 'course'])
            ->first();
    }

    /**
     * Update course average rating and total reviews count.
     */
    private function updateCourseRating(Course $course): void
    {
        $stats = Review::where('course_id', $course->id)
            ->where('is_flagged', false)
            ->selectRaw('AVG(rating) as average_rating, COUNT(*) as total_reviews')
            ->first();

        $course->update([
            'average_rating' => $stats->average_rating ? round($stats->average_rating, 2) : 0,
            'total_reviews' => $stats->total_reviews ?? 0,
        ]);
    }
}

