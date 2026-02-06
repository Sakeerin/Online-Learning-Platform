<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\EnrollmentRequest;
use App\Http\Resources\EnrollmentResource;
use App\Models\Course;
use App\Services\EnrollmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Student - Enrollments
 * @authenticated
 *
 * Endpoints for students to enroll in courses and view their enrollments.
 */
class EnrollmentController extends Controller
{
    public function __construct(
        private EnrollmentService $enrollmentService
    ) {}

    /**
     * Get all enrollments for the authenticated student.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = [
            'is_completed' => $request->boolean('is_completed'),
            'per_page' => $request->query('per_page', 12),
        ];

        $enrollments = $this->enrollmentService->getStudentEnrollments($request->user(), $filters);

        return response()->json([
            'data' => EnrollmentResource::collection($enrollments->items()),
            'meta' => [
                'current_page' => $enrollments->currentPage(),
                'last_page' => $enrollments->lastPage(),
                'per_page' => $enrollments->perPage(),
                'total' => $enrollments->total(),
            ],
        ]);
    }

    /**
     * Enroll in a course.
     */
    public function store(EnrollmentRequest $request): JsonResponse
    {
        $course = Course::findOrFail($request->course_id);

        try {
            $enrollment = $this->enrollmentService->enroll($request->user(), $course);

            return response()->json([
                'message' => 'Successfully enrolled in course',
                'data' => new EnrollmentResource($enrollment->load(['course.instructor', 'course.sections'])),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get enrollment details.
     */
    public function show(string $enrollmentId, Request $request): JsonResponse
    {
        $enrollment = \App\Models\Enrollment::with(['course.instructor', 'course.sections.lessons', 'progress.lesson'])
            ->findOrFail($enrollmentId);

        $this->authorize('view', $enrollment);

        return response()->json([
            'data' => new EnrollmentResource($enrollment),
        ]);
    }
}

