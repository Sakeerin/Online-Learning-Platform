<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Services\ProgressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Student - Progress
 * @authenticated
 *
 * Endpoints for tracking lesson progress, video positions, and completion status.
 */
class ProgressController extends Controller
{
    public function __construct(
        private ProgressService $progressService
    ) {}

    /**
     * Update video playback position.
     */
    public function updatePosition(Request $request, string $enrollmentId, string $lessonId): JsonResponse
    {
        $request->validate([
            'position' => ['required', 'integer', 'min:0'],
        ]);

        $enrollment = Enrollment::findOrFail($enrollmentId);
        $this->authorize('access', $enrollment);

        $lesson = Lesson::findOrFail($lessonId);

        // T158: Update position (debounced API call every 10s handled on frontend)
        $progress = $this->progressService->updatePosition(
            $enrollment,
            $lesson,
            $request->position
        );

        return response()->json([
            'message' => 'Progress updated',
            'data' => new \App\Http\Resources\ProgressResource($progress->load('lesson')),
        ]);
    }

    /**
     * Mark lesson as completed.
     */
    public function markComplete(Request $request, string $enrollmentId, string $lessonId): JsonResponse
    {
        $enrollment = Enrollment::findOrFail($enrollmentId);
        $this->authorize('access', $enrollment);

        $lesson = Lesson::findOrFail($lessonId);

        $progress = $this->progressService->markComplete($enrollment, $lesson);

        return response()->json([
            'message' => 'Lesson marked as complete',
            'data' => new \App\Http\Resources\ProgressResource($progress->load('lesson')),
        ]);
    }

    /**
     * Get progress for a specific lesson.
     */
    public function show(Request $request, string $enrollmentId, string $lessonId): JsonResponse
    {
        $enrollment = Enrollment::findOrFail($enrollmentId);
        $this->authorize('access', $enrollment);

        $lesson = Lesson::findOrFail($lessonId);

        $progress = $this->progressService->getLessonProgress($enrollment, $lesson);

        if (!$progress) {
            return response()->json([
                'data' => null,
            ]);
        }

        return response()->json([
            'data' => new \App\Http\Resources\ProgressResource($progress->load('lesson')),
        ]);
    }

    /**
     * Get all progress for an enrollment.
     */
    public function index(Request $request, string $enrollmentId): JsonResponse
    {
        $enrollment = Enrollment::findOrFail($enrollmentId);
        $this->authorize('access', $enrollment);

        $progress = $this->progressService->getEnrollmentProgress($enrollment);

        return response()->json([
            'data' => \App\Http\Resources\ProgressResource::collection($progress),
        ]);
    }
}

