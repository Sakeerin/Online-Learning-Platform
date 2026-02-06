<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Services\EnrollmentService;
use App\Services\VideoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Student - Learning
 * @authenticated
 *
 * Endpoints for students to access course learning content and video playback.
 */
class LearningController extends Controller
{
    public function __construct(
        private EnrollmentService $enrollmentService,
        private VideoService $videoService
    ) {}

    /**
     * T140: Generate video playback URL with 24-hour expiration.
     */
    public function getVideoUrl(Request $request, string $courseId, string $lessonId): JsonResponse
    {
        $course = Course::findOrFail($courseId);
        $lesson = Lesson::findOrFail($lessonId);

        // T141: Verify enrolled users can access videos
        if (!$this->enrollmentService->checkAccess($request->user(), $course)) {
            // Check if lesson is a preview
            if (!$lesson->is_preview) {
                return response()->json([
                    'message' => 'You must be enrolled in this course to access this lesson.',
                ], 403);
            }
        }

        // Verify lesson belongs to course
        $section = $lesson->section;
        if ($section->course_id !== $course->id) {
            return response()->json([
                'message' => 'Lesson not found in this course.',
            ], 404);
        }

        // Verify lesson is a video
        if (!$lesson->isVideo()) {
            return response()->json([
                'message' => 'This lesson is not a video.',
            ], 422);
        }

        // Get video path from lesson content
        $videoPath = $lesson->content['video_path'] ?? null;
        if (!$videoPath) {
            return response()->json([
                'message' => 'Video not available.',
            ], 404);
        }

        // T139: Generate signed CloudFront URL for authenticated video access
        $playbackUrl = $this->videoService->generatePlaybackUrl($videoPath, 24);

        // Update enrollment last accessed
        $enrollment = $this->enrollmentService->getEnrollment($request->user(), $course);
        if ($enrollment) {
            $enrollment->touchLastAccessed();
        }

        return response()->json([
            'video_url' => $playbackUrl,
            'expires_at' => now()->addHours(24)->toISOString(),
            'lesson' => [
                'id' => $lesson->id,
                'title' => $lesson->title,
                'duration' => $lesson->duration,
            ],
        ]);
    }

    /**
     * Get course learning data (sections, lessons, progress).
     */
    public function getCourseLearning(Request $request, string $courseId): JsonResponse
    {
        $course = Course::with(['sections.lessons'])->findOrFail($courseId);

        // T141: Verify enrolled users can access
        if (!$this->enrollmentService->checkAccess($request->user(), $course)) {
            return response()->json([
                'message' => 'You must be enrolled in this course to access learning content.',
            ], 403);
        }

        $enrollment = $this->enrollmentService->getEnrollment($request->user(), $course);

        if (!$enrollment) {
            return response()->json([
                'message' => 'Enrollment not found.',
            ], 404);
        }

        // Get progress for all lessons
        $progress = $enrollment->progress()->with('lesson')->get()->keyBy('lesson_id');

        // Add progress data to lessons
        $sections = $course->sections->map(function ($section) use ($progress) {
            $section->lessons = $section->lessons->map(function ($lesson) use ($progress) {
                $lessonProgress = $progress->get($lesson->id);
                $lesson->progress = $lessonProgress ? [
                    'is_completed' => $lessonProgress->is_completed,
                    'video_position' => $lessonProgress->video_position,
                ] : null;
                return $lesson;
            });
            return $section;
        });

        return response()->json([
            'data' => [
                'course' => new \App\Http\Resources\CourseDetailResource($course),
                'enrollment' => new \App\Http\Resources\EnrollmentResource($enrollment),
                'sections' => \App\Http\Resources\SectionResource::collection($sections),
            ],
        ]);
    }
}

