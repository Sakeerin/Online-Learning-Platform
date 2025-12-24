<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Course;
use App\Models\Review;
use App\Services\ReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct(
        private ReviewService $reviewService
    ) {}

    /**
     * Get all reviews for a course.
     */
    public function index(Request $request, string $courseId): JsonResponse
    {
        $course = Course::findOrFail($courseId);

        $filters = [
            'rating' => $request->query('rating'),
            'sort_by' => $request->query('sort_by', 'created_at'),
            'sort_order' => $request->query('sort_order', 'desc'),
            'per_page' => $request->query('per_page', 10),
        ];

        $reviews = $this->reviewService->getCourseReviews($course, $filters);

        return response()->json([
            'data' => ReviewResource::collection($reviews->items()),
            'meta' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
            ],
        ]);
    }

    /**
     * Get a specific review.
     */
    public function show(string $courseId, string $reviewId): JsonResponse
    {
        $review = Review::with(['user', 'course'])->findOrFail($reviewId);

        // Verify review belongs to course
        if ($review->course_id !== $courseId) {
            return response()->json([
                'message' => 'Review not found for this course.',
            ], 404);
        }

        return response()->json([
            'data' => new ReviewResource($review),
        ]);
    }

    /**
     * Create a new review.
     */
    public function store(ReviewRequest $request, string $courseId): JsonResponse
    {
        $course = Course::findOrFail($courseId);

        try {
            $review = $this->reviewService->create(
                $request->user(),
                $course,
                $request->validated()
            );

            return response()->json([
                'message' => 'Review created successfully',
                'data' => new ReviewResource($review),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Update an existing review.
     */
    public function update(ReviewRequest $request, string $courseId, string $reviewId): JsonResponse
    {
        $review = Review::findOrFail($reviewId);

        // Verify review belongs to course
        if ($review->course_id !== $courseId) {
            return response()->json([
                'message' => 'Review not found for this course.',
            ], 404);
        }

        $this->authorize('update', $review);

        try {
            $review = $this->reviewService->update($review, $request->validated());

            return response()->json([
                'message' => 'Review updated successfully',
                'data' => new ReviewResource($review),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Delete a review.
     */
    public function destroy(string $courseId, string $reviewId): JsonResponse
    {
        $review = Review::findOrFail($reviewId);

        // Verify review belongs to course
        if ($review->course_id !== $courseId) {
            return response()->json([
                'message' => 'Review not found for this course.',
            ], 404);
        }

        $this->authorize('delete', $review);

        $this->reviewService->delete($review);

        return response()->json([
            'message' => 'Review deleted successfully',
        ]);
    }

    /**
     * Flag a review for moderation.
     */
    public function flag(Request $request, string $courseId, string $reviewId): JsonResponse
    {
        $review = Review::findOrFail($reviewId);

        // Verify review belongs to course
        if ($review->course_id !== $courseId) {
            return response()->json([
                'message' => 'Review not found for this course.',
            ], 404);
        }

        $this->authorize('flag', $review);

        $review = $this->reviewService->flag($review);

        return response()->json([
            'message' => 'Review flagged for moderation',
            'data' => new ReviewResource($review),
        ]);
    }

    /**
     * Get the authenticated user's review for a course.
     */
    public function myReview(Request $request, string $courseId): JsonResponse
    {
        $course = Course::findOrFail($courseId);

        $review = $this->reviewService->getUserReview($request->user(), $course);

        if (!$review) {
            return response()->json([
                'data' => null,
            ]);
        }

        return response()->json([
            'data' => new ReviewResource($review->load(['user', 'course'])),
        ]);
    }

    /**
     * Add instructor response to a review.
     */
    public function respond(Request $request, string $courseId, string $reviewId): JsonResponse
    {
        $request->validate([
            'response' => ['required', 'string', 'max:1000'],
        ]);

        $review = Review::findOrFail($reviewId);

        // Verify review belongs to course
        if ($review->course_id !== $courseId) {
            return response()->json([
                'message' => 'Review not found for this course.',
            ], 404);
        }

        $this->authorize('respond', $review);

        try {
            $review = $this->reviewService->addInstructorResponse(
                $review,
                $request->input('response'),
                $request->user()
            );

            return response()->json([
                'message' => 'Response added successfully',
                'data' => new ReviewResource($review),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}

