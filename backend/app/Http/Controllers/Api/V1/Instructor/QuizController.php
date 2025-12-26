<?php

namespace App\Http\Controllers\Api\V1\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateQuizRequest;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Services\QuizService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function __construct(
        private QuizService $quizService
    ) {}

    /**
     * Create a quiz for a lesson.
     */
    public function store(CreateQuizRequest $request, Course $course, Lesson $lesson): JsonResponse
    {
        // Verify lesson belongs to course
        if ($lesson->section->course_id !== $course->id) {
            return response()->json([
                'message' => 'Lesson not found in this course',
            ], 404);
        }

        // Verify user owns the course
        if ($course->instructor_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        // Verify lesson is of type quiz
        if ($lesson->type !== 'quiz') {
            return response()->json([
                'message' => 'Lesson must be of type quiz',
            ], 422);
        }

        try {
            $quiz = $this->quizService->create($lesson, $request->validated());

            return response()->json([
                'message' => 'Quiz created successfully',
                'data' => $quiz->load('questions'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Update a quiz.
     */
    public function update(CreateQuizRequest $request, Course $course, Lesson $lesson, Quiz $quiz): JsonResponse
    {
        // Verify quiz belongs to lesson
        if ($quiz->lesson_id !== $lesson->id) {
            return response()->json([
                'message' => 'Quiz not found for this lesson',
            ], 404);
        }

        // Verify lesson belongs to course
        if ($lesson->section->course_id !== $course->id) {
            return response()->json([
                'message' => 'Lesson not found in this course',
            ], 404);
        }

        // Verify user owns the course
        if ($course->instructor_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        try {
            $quiz = $this->quizService->update($quiz, $request->validated());

            return response()->json([
                'message' => 'Quiz updated successfully',
                'data' => $quiz->load('questions'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get quiz details (with correct answers for instructor).
     */
    public function show(Request $request, Course $course, Lesson $lesson, Quiz $quiz): JsonResponse
    {
        // Verify quiz belongs to lesson
        if ($quiz->lesson_id !== $lesson->id) {
            return response()->json([
                'message' => 'Quiz not found for this lesson',
            ], 404);
        }

        // Verify lesson belongs to course
        if ($lesson->section->course_id !== $course->id) {
            return response()->json([
                'message' => 'Lesson not found in this course',
            ], 404);
        }

        // Verify user owns the course
        if ($course->instructor_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        return response()->json([
            'data' => $quiz->load('questions'),
        ]);
    }

    /**
     * Delete a quiz.
     */
    public function destroy(Course $course, Lesson $lesson, Quiz $quiz): JsonResponse
    {
        // Verify quiz belongs to lesson
        if ($quiz->lesson_id !== $lesson->id) {
            return response()->json([
                'message' => 'Quiz not found for this lesson',
            ], 404);
        }

        // Verify lesson belongs to course
        if ($lesson->section->course_id !== $course->id) {
            return response()->json([
                'message' => 'Lesson not found in this course',
            ], 404);
        }

        // Verify user owns the course
        if ($course->instructor_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $quiz->delete();

        return response()->json([
            'message' => 'Quiz deleted successfully',
        ]);
    }
}

