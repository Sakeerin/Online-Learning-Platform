<?php

namespace App\Http\Controllers\Api\V1\Instructor;

use App\Actions\PublishCourseAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Http\Resources\CourseDetailResource;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use App\Policies\CoursePolicy;
use App\Services\CourseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function __construct(
        private CourseService $courseService,
        private PublishCourseAction $publishCourseAction
    ) {}

    /**
     * Display a listing of the instructor's courses.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = [
            'status' => $request->query('status'),
            'per_page' => $request->query('per_page', 15),
        ];

        $courses = $this->courseService->getInstructorCourses($request->user(), $filters);

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
     * Store a newly created course.
     */
    public function store(CreateCourseRequest $request): JsonResponse
    {
        $course = $this->courseService->create($request->user(), $request->validated());

        return response()->json([
            'message' => 'Course created successfully',
            'data' => new CourseDetailResource($course->load(['instructor', 'sections.lessons'])),
        ], 201);
    }

    /**
     * Display the specified course.
     */
    public function show(Course $course): JsonResponse
    {
        $this->authorize('view', $course);

        $course->load(['instructor', 'sections.lessons']);

        return response()->json([
            'data' => new CourseDetailResource($course),
        ]);
    }

    /**
     * Update the specified course.
     */
    public function update(UpdateCourseRequest $request, Course $course): JsonResponse
    {
        $this->authorize('update', $course);

        $course = $this->courseService->update($course, $request->validated());

        return response()->json([
            'message' => 'Course updated successfully',
            'data' => new CourseDetailResource($course->load(['instructor', 'sections.lessons'])),
        ]);
    }

    /**
     * Remove the specified course.
     */
    public function destroy(Course $course): JsonResponse
    {
        $this->authorize('delete', $course);

        $this->courseService->delete($course);

        return response()->json([
            'message' => 'Course deleted successfully',
        ]);
    }

    /**
     * Publish the specified course.
     */
    public function publish(Course $course): JsonResponse
    {
        $this->authorize('publish', $course);

        try {
            $course = $this->publishCourseAction->execute($course);

            return response()->json([
                'message' => 'Course published successfully',
                'data' => new CourseDetailResource($course->load(['instructor', 'sections.lessons'])),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Unpublish the specified course.
     */
    public function unpublish(Course $course): JsonResponse
    {
        $this->authorize('unpublish', $course);

        $course = $this->courseService->unpublish($course);

        return response()->json([
            'message' => 'Course unpublished successfully',
            'data' => new CourseDetailResource($course->load(['instructor', 'sections.lessons'])),
        ]);
    }
}

