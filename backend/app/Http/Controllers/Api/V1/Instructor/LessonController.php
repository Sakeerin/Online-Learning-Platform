<?php

namespace App\Http\Controllers\Api\V1\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadVideoRequest;
use App\Http\Resources\LessonResource;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Section;
use App\Services\VideoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LessonController extends Controller
{
    public function __construct(
        private VideoService $videoService
    ) {}

    /**
     * Store a newly created lesson.
     */
    public function store(Request $request, Course $course, Section $section): JsonResponse
    {
        $this->authorize('update', $course);

        // Ensure section belongs to course
        if ($section->course_id !== $course->id) {
            return response()->json([
                'message' => 'Section not found in this course',
            ], 404);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:video,quiz,article'],
            'content' => ['required', 'array'],
            'duration' => ['nullable', 'integer', 'min:0'],
            'is_preview' => ['nullable', 'boolean'],
            'order' => ['nullable', 'integer', 'min:1'],
        ]);

        $lesson = DB::transaction(function () use ($section, $validated) {
            // If no order specified, add to end
            if (!isset($validated['order'])) {
                $maxOrder = $section->lessons()->max('order') ?? 0;
                $validated['order'] = $maxOrder + 1;
            }

            return $section->lessons()->create($validated);
        });

        return response()->json([
            'message' => 'Lesson created successfully',
            'data' => new LessonResource($lesson),
        ], 201);
    }

    /**
     * Update the specified lesson.
     */
    public function update(Request $request, Course $course, Section $section, Lesson $lesson): JsonResponse
    {
        $this->authorize('update', $course);

        // Ensure lesson belongs to section and course
        if ($lesson->section_id !== $section->id || $section->course_id !== $course->id) {
            return response()->json([
                'message' => 'Lesson not found in this course',
            ], 404);
        }

        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'type' => ['sometimes', 'string', 'in:video,quiz,article'],
            'content' => ['sometimes', 'array'],
            'duration' => ['nullable', 'integer', 'min:0'],
            'is_preview' => ['nullable', 'boolean'],
            'order' => ['sometimes', 'integer', 'min:1'],
        ]);

        $lesson->update($validated);

        return response()->json([
            'message' => 'Lesson updated successfully',
            'data' => new LessonResource($lesson),
        ]);
    }

    /**
     * Remove the specified lesson.
     */
    public function destroy(Course $course, Section $section, Lesson $lesson): JsonResponse
    {
        $this->authorize('update', $course);

        // Ensure lesson belongs to section and course
        if ($lesson->section_id !== $section->id || $section->course_id !== $course->id) {
            return response()->json([
                'message' => 'Lesson not found in this course',
            ], 404);
        }

        // Delete video file if it's a video lesson
        if ($lesson->isVideo() && isset($lesson->content['video_path'])) {
            $this->videoService->deleteVideo($lesson->content['video_path']);
        }

        $lesson->delete();

        return response()->json([
            'message' => 'Lesson deleted successfully',
        ]);
    }

    /**
     * Upload video for a lesson.
     */
    public function uploadVideo(UploadVideoRequest $request, Course $course, Section $section, Lesson $lesson): JsonResponse
    {
        $this->authorize('update', $course);

        // Ensure lesson belongs to section and course
        if ($lesson->section_id !== $section->id || $section->course_id !== $course->id) {
            return response()->json([
                'message' => 'Lesson not found in this course',
            ], 404);
        }

        // Ensure lesson is a video type
        if (!$lesson->isVideo()) {
            return response()->json([
                'message' => 'Lesson must be of type video',
            ], 422);
        }

        $videoFile = $request->file('video');
        $videoPath = $this->videoService->storeVideo(
            $videoFile,
            $course->id,
            $lesson->id
        );

        // Update lesson content with video path
        $content = $lesson->content ?? [];
        $content['video_path'] = $videoPath;
        $content['video_url'] = $this->videoService->generatePlaybackUrl($videoPath);
        $content['filename'] = $request->input('filename', $videoFile->getClientOriginalName());

        $lesson->update([
            'content' => $content,
            'duration' => $request->input('duration'), // Can be extracted from video metadata
        ]);

        return response()->json([
            'message' => 'Video uploaded successfully',
            'data' => new LessonResource($lesson),
        ]);
    }

    /**
     * Reorder lessons within a section.
     */
    public function reorder(Request $request, Course $course, Section $section): JsonResponse
    {
        $this->authorize('update', $course);

        // Ensure section belongs to course
        if ($section->course_id !== $course->id) {
            return response()->json([
                'message' => 'Section not found in this course',
            ], 404);
        }

        $validated = $request->validate([
            'lessons' => ['required', 'array'],
            'lessons.*.id' => ['required', 'uuid', 'exists:lessons,id'],
            'lessons.*.order' => ['required', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($section, $validated) {
            foreach ($validated['lessons'] as $lessonData) {
                $lesson = Lesson::find($lessonData['id']);
                if ($lesson && $lesson->section_id === $section->id) {
                    $lesson->update(['order' => $lessonData['order']]);
                }
            }
        });

        return response()->json([
            'message' => 'Lessons reordered successfully',
        ]);
    }
}

