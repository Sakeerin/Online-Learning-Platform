<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseDetailResource;
use App\Http\Resources\CourseResource;
use App\Services\CourseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Course Discovery
 *
 * Public endpoints for browsing, searching, and viewing course details.
 */
class CourseDiscoveryController extends Controller
{
    public function __construct(
        private CourseService $courseService
    ) {}

    /**
     * T100: Browse courses with pagination and filters.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = [
            'category' => $request->query('category'),
            'subcategory' => $request->query('subcategory'),
            'difficulty_level' => $request->query('difficulty_level'),
            'min_price' => $request->query('min_price'),
            'max_price' => $request->query('max_price'),
            'free_only' => $request->boolean('free_only'),
            'min_rating' => $request->query('min_rating'),
            'sort_by' => $request->query('sort_by', 'relevance'),
            'per_page' => $request->query('per_page', 12),
        ];

        $courses = $this->courseService->browseCourses($filters);

        return response()->json([
            'data' => CourseResource::collection($courses->items()),
            'meta' => [
                'current_page' => $courses->currentPage(),
                'last_page' => $courses->lastPage(),
                'per_page' => $courses->perPage(),
                'total' => $courses->total(),
            ],
        ]);
    }

    /**
     * T101: Search courses with full-text search.
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => ['required', 'string', 'min:2', 'max:255'],
        ]);

        $query = $request->query('q');
        $filters = [
            'category' => $request->query('category'),
            'difficulty_level' => $request->query('difficulty_level'),
            'min_price' => $request->query('min_price'),
            'max_price' => $request->query('max_price'),
            'min_rating' => $request->query('min_rating'),
            'per_page' => $request->query('per_page', 12),
        ];

        $courses = $this->courseService->searchCourses($query, $filters);

        return response()->json([
            'data' => CourseResource::collection($courses->items()),
            'meta' => [
                'current_page' => $courses->currentPage(),
                'last_page' => $courses->lastPage(),
                'per_page' => $courses->perPage(),
                'total' => $courses->total(),
                'query' => $query,
            ],
        ]);
    }

    /**
     * Get featured courses for homepage.
     */
    public function featured(): JsonResponse
    {
        $courses = $this->courseService->getFeaturedCourses(6);

        return response()->json([
            'data' => CourseResource::collection($courses),
        ]);
    }

    /**
     * T102: Get course detail for public viewing.
     */
    public function show(string $courseId): JsonResponse
    {
        $course = $this->courseService->getPublicCourse($courseId);

        if (!$course) {
            return response()->json([
                'message' => 'Course not found or not published',
            ], 404);
        }

        return response()->json([
            'data' => new CourseDetailResource($course),
        ]);
    }
}

