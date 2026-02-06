<?php

namespace App\Http\Controllers\Api\V1\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\AnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Instructor - Analytics
 * @authenticated
 *
 * Endpoints for instructors to view enrollment, revenue, and course performance analytics.
 */
class AnalyticsController extends Controller
{
    public function __construct(
        private AnalyticsService $analyticsService
    ) {}

    /**
     * Get overall analytics for the instructor.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = [
            'date_from' => $request->query('date_from') ? now()->parse($request->query('date_from'))->startOfDay() : null,
            'date_to' => $request->query('date_to') ? now()->parse($request->query('date_to'))->endOfDay() : null,
        ];

        $analytics = $this->analyticsService->getInstructorAnalytics($request->user(), $filters);

        return response()->json([
            'data' => $analytics,
        ]);
    }

    /**
     * Get analytics for a specific course.
     */
    public function course(Request $request, Course $course): JsonResponse
    {
        $filters = [
            'date_from' => $request->query('date_from') ? now()->parse($request->query('date_from'))->startOfDay() : null,
            'date_to' => $request->query('date_to') ? now()->parse($request->query('date_to'))->endOfDay() : null,
        ];

        try {
            $analytics = $this->analyticsService->getCourseAnalytics($course, $request->user(), $filters);

            return response()->json([
                'data' => $analytics,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 403);
        }
    }

    /**
     * Get revenue breakdown for the instructor.
     */
    public function revenue(Request $request): JsonResponse
    {
        $filters = [
            'date_from' => $request->query('date_from') ? now()->parse($request->query('date_from'))->startOfDay() : null,
            'date_to' => $request->query('date_to') ? now()->parse($request->query('date_to'))->endOfDay() : null,
            'group_by' => $request->query('group_by', 'course'), // course, day, month
        ];

        $breakdown = $this->analyticsService->getRevenueBreakdown($request->user(), $filters);

        return response()->json([
            'data' => $breakdown,
        ]);
    }

    /**
     * Get lesson analytics for a course.
     */
    public function lessons(Request $request, Course $course): JsonResponse
    {
        try {
            $analytics = $this->analyticsService->getLessonAnalytics($course);

            // Verify instructor owns the course
            if ($course->instructor_id !== $request->user()->id) {
                return response()->json([
                    'message' => 'You are not authorized to view analytics for this course.',
                ], 403);
            }

            return response()->json([
                'data' => $analytics,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}

